<?php

namespace App\Http\Controllers;

use App\Models\KartuKeluarga;
use App\Models\User;
use App\Models\Warga;
use App\Models\WargaAnak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OnboardingController extends Controller
{
    /**
     * Tampilkan halaman formulir Onboarding Warga
     */
    public function index()
    {
        $user = Auth::user();

        // Cari profil data warga yang terhubung
        $warga = Warga::where('user_id', $user->id)
            ->orWhere('no_hp', $user->no_hp)
            ->orWhere('nama_lengkap', $user->name)
            ->first();

        $kk = KartuKeluarga::where('user_id', $user->id)
            ->orWhere('warga_id', $warga?->id)
            ->first();

        $anaks = $warga ? $warga->anaks : collect();

        return view('warga.onboarding', compact('user', 'warga', 'kk', 'anaks'));
    }

    /**
     * Simpan kelengkapan data Onboarding Warga
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $wargaExisting = Warga::where('user_id', $user->id)
            ->orWhere('no_hp', $user->no_hp)
            ->orWhere('nama_lengkap', $user->name)
            ->first();

        // 1. Validasi dasar untuk semua warga
        $rules = [
            'tempat_lahir' => ['required', 'string', 'max:100'],
            'tanggal_lahir' => ['required', 'date'],
            'status_domisili' => ['required', 'in:tetap,kontrak'],
            'nomor_rumah' => ['required', 'string', 'max:100'],
            'is_berkeluarga' => ['required', 'in:0,1'],
        ];

        // 2. Validasi kondisional jika memilih Berkeluarga = "Ya"
        if ($request->input('is_berkeluarga') == '1') {
            $rules['nik'] = ['required', 'string', 'regex:/^[0-9]{16}$/'];
            $rules['no_kk'] = ['required', 'string', 'regex:/^[0-9]{16}$/'];
            
            // Wajib upload file kecuali jika sudah pernah mengunggah sebelumnya
            $hasExistingKtp = !empty($wargaExisting?->file_ktp);
            $hasExistingKk = !empty($wargaExisting?->file_kk);

            $rules['file_ktp'] = [$hasExistingKtp ? 'nullable' : 'required', 'file', 'mimes:jpeg,png,jpg,pdf', 'max:5120'];
            $rules['file_kk'] = [$hasExistingKk ? 'nullable' : 'required', 'file', 'mimes:jpeg,png,jpg,pdf', 'max:5120'];
            $rules['memiliki_anak'] = ['required', 'in:0,1'];

            // 3. Validasi kondisional jika Memiliki Anak = "Ya"
            if ($request->input('memiliki_anak') == '1') {
                $rules['anak'] = ['required', 'array', 'min:1'];
                $rules['anak.*.nama'] = ['required', 'string', 'max:255'];
                $rules['anak.*.umur'] = ['required', 'numeric', 'min:0', 'max:100'];
                $rules['anak.*.tempat_lahir'] = ['nullable', 'string', 'max:100'];
                $rules['anak.*.tanggal_lahir'] = ['nullable', 'date'];
            }
        }

        $messages = [
            'tempat_lahir.required' => 'Tempat lahir wajib diisi.',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi.',
            'status_domisili.required' => 'Status domisili wajib dipilih (Tetap atau Kontrak/Kos).',
            'nomor_rumah.required' => 'Blok / Nomor rumah wajib diisi.',
            'is_berkeluarga.required' => 'Pilihan status berkeluarga wajib ditentukan.',
            'nik.required' => 'NIK wajib diisi.',
            'nik.regex' => 'NIK wajib terdiri dari 16 digit angka.',
            'no_kk.required' => 'Nomor Kartu Keluarga (KK) wajib diisi.',
            'no_kk.regex' => 'Nomor KK wajib terdiri tepat 16 digit angka.',
            'file_ktp.required' => 'File softcopy KTP wajib diunggah.',
            'file_ktp.mimes' => 'Format file KTP harus berupa JPG, PNG, atau PDF.',
            'file_ktp.max' => 'Ukuran file KTP maksimal 5MB.',
            'file_kk.required' => 'File softcopy KK wajib diunggah.',
            'file_kk.mimes' => 'Format file KK harus berupa JPG, PNG, atau PDF.',
            'file_kk.max' => 'Ukuran file KK maksimal 5MB.',
            'memiliki_anak.required' => 'Silakan pilih apakah Anda memiliki anak.',
            'anak.required' => 'Silakan tambahkan minimal satu data anak jika memilih memiliki anak.',
            'anak.*.nama.required' => 'Nama lengkap anak wajib diisi.',
            'anak.*.umur.required' => 'Umur anak wajib diisi angka.',
        ];

        $validated = $request->validate($rules, $messages);

        DB::transaction(function () use ($request, $user, $validated, $wargaExisting) {
            $isBerkeluarga = $validated['is_berkeluarga'] == '1';
            $memilikiAnak = $isBerkeluarga && ($request->input('memiliki_anak') == '1');

            // 1. Simpan atau perbarui data Warga
            $warga = $wargaExisting ?: new Warga();
            $warga->user_id = $user->id;
            $warga->nama_lengkap = $user->name;
            $warga->tempat_lahir = $validated['tempat_lahir'];
            $warga->tanggal_lahir = $validated['tanggal_lahir'];
            $warga->status_tinggal = $validated['status_domisili'];
            $warga->nomor_rumah = $validated['nomor_rumah'];
            $warga->no_hp = $user->no_hp ?: ($warga->no_hp ?: $user->username);
            $warga->is_berkeluarga = $isBerkeluarga;
            $warga->memiliki_anak = $memilikiAnak;
            $warga->is_profile_complete = true;

            // Handle penyimpanan berkas di storage private (bukan public)
            if ($isBerkeluarga) {
                $warga->nik = $validated['nik'];
                $warga->no_kk = $validated['no_kk'];

                if ($request->hasFile('file_ktp')) {
                    if ($warga->file_ktp && Storage::disk('local')->exists($warga->file_ktp)) {
                        Storage::disk('local')->delete($warga->file_ktp);
                    }
                    $warga->file_ktp = $request->file('file_ktp')->store('dokumen_rahasia_warga', 'local');
                }

                if ($request->hasFile('file_kk')) {
                    if ($warga->file_kk && Storage::disk('local')->exists($warga->file_kk)) {
                        Storage::disk('local')->delete($warga->file_kk);
                    }
                    $warga->file_kk = $request->file('file_kk')->store('dokumen_rahasia_warga', 'local');
                }
            }

            $warga->save();

            // 2. Simpan atau perbarui data Kartu Keluarga jika berkeluarga
            $kk = null;
            if ($isBerkeluarga) {
                $kk = KartuKeluarga::where('user_id', $user->id)
                    ->orWhere('warga_id', $warga->id)
                    ->orWhere('nomor_kk', $validated['no_kk'])
                    ->first();

                if (!$kk) {
                    $kk = new KartuKeluarga();
                }

                $anakCount = ($memilikiAnak && is_array($request->anak)) ? count($request->anak) : 0;

                $kk->user_id = $user->id;
                $kk->warga_id = $warga->id;
                $kk->nomor_kk = $validated['no_kk'];
                $kk->kepala_keluarga = $user->name;
                $kk->nik_kepala_keluarga = $validated['nik'];
                $kk->nomor_rumah = $validated['nomor_rumah'];
                $kk->no_hp = $user->no_hp ?: $warga->no_hp;
                $kk->foto_ktp = $warga->file_ktp ?: $kk->foto_ktp;
                $kk->foto_kk = $warga->file_kk ?: $kk->foto_kk;
                $kk->jumlah_anggota = 1 + $anakCount;
                $kk->status_verifikasi = 'menunggu';
                $kk->save();

                $warga->kartu_keluarga_id = $kk->id;
                $warga->save();
            }

            // 3. Simpan data anak jika ada (relasi hasMany warga_anaks)
            $warga->anaks()->delete(); // Hapus data lama sebelum merefresh
            if ($memilikiAnak && is_array($request->anak)) {
                foreach ($request->anak as $item) {
                    if (!empty($item['nama'])) {
                        $warga->anaks()->create([
                            'kartu_keluarga_id' => $kk?->id,
                            'nama_anak' => trim($item['nama']),
                            'umur' => $item['umur'] ?? null,
                            'tempat_lahir' => $item['tempat_lahir'] ?? null,
                            'tanggal_lahir' => $item['tanggal_lahir'] ?? null,
                        ]);
                    }
                }
            }

            // 4. Perbarui status akun User agar is_profile_complete menjadi true dan simpan no_kk untuk login
            $user->is_profile_complete = true;
            if ($isBerkeluarga && !empty($validated['no_kk'])) {
                $user->no_kk = $validated['no_kk'];
            }
            $user->save();
        });

        return redirect()->route('warga.dashboard')->with('success', 'Selamat! Profil Anda berhasil dilengkapi. Seluruh layanan kependudukan kini dapat diakses.');
    }

    /**
     * Download/Lihat Berkas Dokumen Private dengan Verifikasi Otoritas
     */
    public function downloadDokumen(Request $request, $type)
    {
        $user = Auth::user();

        $warga = Warga::where('user_id', $user->id)
            ->orWhere('no_hp', $user->no_hp)
            ->orWhere('nama_lengkap', $user->name)
            ->first();

        if (!$warga) {
            abort(404, 'Data warga tidak ditemukan.');
        }

        $path = $type === 'ktp' ? $warga->file_ktp : ($type === 'kk' ? $warga->file_kk : null);

        if (!$path || !Storage::disk('local')->exists($path)) {
            abort(404, 'File dokumen tidak ditemukan di server.');
        }

        return Storage::disk('local')->response($path);
    }
}

