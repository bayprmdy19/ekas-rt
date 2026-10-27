<?php

namespace App\Http\Controllers;

use App\Models\KartuKeluarga;
use App\Models\Warga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class WargaController extends Controller
{
    /**
     * Tampilkan Halaman Admin Data Warga dengan Filter & Pencarian
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak: Halaman tersebut khusus Pengurus RT.');
        }

        $search = trim($request->get('q', ''));
        $blok = $request->get('blok', '');
        $statusKeluarga = $request->get('status_keluarga', '');
        $statusDomisili = $request->get('status_domisili', '');

        // Query warga menggunakan Eloquent when() (termasuk relasi Kartu Keluarga)
        $wargas = Warga::with('kartuKeluarga')
            ->when($search, function ($query, $search) {
                $query->where(function ($sub) use ($search) {
                    $sub->where('nama_lengkap', 'like', "%{$search}%")
                        ->orWhere('no_kk', 'like', "%{$search}%")
                        ->orWhere('no_hp', 'like', "%{$search}%")
                        ->orWhere('nik', 'like', "%{$search}%")
                        ->orWhere('nomor_rumah', 'like', "%{$search}%")
                        ->orWhere('blok', 'like', "%{$search}%")
                        ->orWhereHas('kartuKeluarga', function ($kkQuery) use ($search) {
                            $kkQuery->where('nomor_kk', 'like', "%{$search}%")
                                ->orWhere('kepala_keluarga', 'like', "%{$search}%")
                                ->orWhere('no_hp', 'like', "%{$search}%");
                        });
                });
            })
            ->when($blok, function ($query, $blok) {
                if ($blok === 'Belum Diisi') {
                    $query->where(function ($sub) {
                        $sub->whereNull('nomor_rumah')
                            ->orWhere('nomor_rumah', '')
                            ->orWhere('nomor_rumah', '-');
                    });
                } else {
                    $query->where(function ($sub) use ($blok) {
                        $sub->where('blok', $blok)
                            ->orWhere('nomor_rumah', 'like', "%{$blok}%");
                    });
                }
            })
            ->when($statusKeluarga, function ($query, $statusKeluarga) {
                $query->where('status_keluarga', $statusKeluarga);
            })
            ->when($statusDomisili, function ($query, $statusDomisili) {
                // status_domisili pada DB adalah status_tinggal ('tetap' / 'kontrak')
                $statusVal = strtolower($statusDomisili) === 'kontrak/kos' ? 'kontrak' : strtolower($statusDomisili);
                $query->where('status_tinggal', $statusVal);
            })
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        // Daftar Blok untuk dropdown select
        $blokList = ['Blok A', 'Blok B', 'Blok C', 'Blok D', 'Blok E'];

        // Daftar Status Keluarga untuk dropdown select
        $statusKeluargaList = ['Kepala Keluarga', 'Istri', 'Anak', 'Famili Lain'];

        // Daftar Status Domisili untuk dropdown select
        $statusDomisiliList = ['Tetap', 'Kontrak/Kos'];

        // Metrik Ringkasan Cepat
        $totalWarga = Warga::count();
        $totalTetap = Warga::where('status_tinggal', 'tetap')->count();
        $totalKontrak = Warga::where('status_tinggal', 'kontrak')->count();
        $totalKepalaKeluarga = Warga::where('status_keluarga', 'Kepala Keluarga')->count();

        return view('admin.warga.index', compact(
            'wargas',
            'blokList',
            'statusKeluargaList',
            'statusDomisiliList',
            'search',
            'blok',
            'statusKeluarga',
            'statusDomisili',
            'totalWarga',
            'totalTetap',
            'totalKontrak',
            'totalKepalaKeluarga'
        ));
    }

    /**
     * Simpan Data Warga Baru (Sinkron dengan Form Registrasi Warga)
     */
    public function store(Request $request)
    {
        // Normalisasi nama field agar mendukung form registrasi baru (name/nomor_kk/no_whatsapp) maupun field tabel wargas (nama_lengkap/no_kk/no_hp)
        $request->merge([
            'nama_lengkap' => $request->input('nama_lengkap') ?: $request->input('name'),
            'no_kk' => $request->input('no_kk') ?: $request->input('nomor_kk'),
            'no_hp' => $request->input('no_hp') ?: $request->input('no_whatsapp'),
        ]);

        $rules = [
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'no_kk' => ['required', 'string', 'digits:16'],
            'no_hp' => ['nullable', 'string', 'regex:/^[0-9]+$/', 'max:13'],
            'nik' => ['nullable', 'string', 'max:20', 'unique:wargas,nik'],
            'status_keluarga' => ['nullable', 'string', 'in:Kepala Keluarga,Istri,Anak,Famili Lain'],
            'nomor_rumah' => ['nullable', 'string', 'max:100'],
            'blok' => ['nullable', 'string', 'max:50'],
            'status_tinggal' => ['nullable', 'in:tetap,kontrak,kos'],
        ];

        // Jika form menyertakan input no_whatsapp / form tambah warga baru, wajibkan no_hp dan validasi password
        if ($request->has('no_whatsapp') || $request->has('password')) {
            $rules['no_hp'] = ['required', 'string', 'regex:/^[0-9]+$/', 'max:13'];
        }

        if ($request->filled('password') || $request->has('password_confirmation')) {
            $rules['password'] = [
                'required',
                'confirmed',
                \Illuminate\Validation\Rules\Password::min(8)->letters()->numbers(),
            ];
        }

        $validated = $request->validate($rules, [
            'nama_lengkap.required' => 'Nama kepala keluarga wajib diisi.',
            'no_kk.required' => 'Nomor KK wajib diisi.',
            'no_kk.digits' => 'Nomor KK wajib berjumlah tepat 16 digit angka.',
            'no_hp.required' => 'Nomor WhatsApp wajib diisi.',
            'no_hp.regex' => 'Nomor WhatsApp hanya boleh berisi angka.',
            'no_hp.max' => 'Nomor WhatsApp maksimal 13 digit angka.',
            'nik.unique' => 'NIK sudah terdaftar pada data warga lain.',
            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal 8 karakter.',
            'password' => 'Password wajib berupa kombinasi huruf dan angka, minimal 8 karakter.',
        ]);

        $validated['status_keluarga'] = $validated['status_keluarga'] ?? 'Kepala Keluarga';
        $validated['nomor_rumah'] = !empty($validated['nomor_rumah']) ? $validated['nomor_rumah'] : '-';
        $statusTinggal = strtolower($validated['status_tinggal'] ?? 'tetap');
        $validated['status_tinggal'] = $statusTinggal === 'kos' ? 'kontrak' : $statusTinggal;

        // Auto deteksi blok jika tidak diisi manual
        if (empty($validated['blok']) && preg_match('/(Blok\s+[A-Za-z0-9]+)/i', $validated['nomor_rumah'], $match)) {
            $validated['blok'] = ucwords(strtolower($match[1]));
        }

        // Cek apakah ada KartuKeluarga yang cocok untuk dihubungkan
        if (!empty($validated['no_kk'])) {
            $kk = KartuKeluarga::where('nomor_kk', $validated['no_kk'])->first();
            if ($kk) {
                $validated['kartu_keluarga_id'] = $kk->id;
            }
        }

        $warga = Warga::create([
            'nama_lengkap' => $validated['nama_lengkap'],
            'nik' => $validated['nik'] ?? null,
            'no_kk' => $validated['no_kk'],
            'status_keluarga' => $validated['status_keluarga'],
            'nomor_rumah' => $validated['nomor_rumah'],
            'blok' => $validated['blok'] ?? null,
            'no_hp' => $validated['no_hp'] ?? null,
            'status_tinggal' => $validated['status_tinggal'],
            'kartu_keluarga_id' => $validated['kartu_keluarga_id'] ?? null,
        ]);

        // Sinkronisasi pembuatan akun login User jika belum ada akun dengan Nomor KK tersebut
        if (!empty($validated['no_kk'])) {
            $existingUser = \App\Models\User::where('username', $validated['no_kk'])->first();
            if (!$existingUser && !empty($request->password)) {
                $newUser = \App\Models\User::create([
                    'name' => $validated['nama_lengkap'],
                    'username' => $validated['no_kk'],
                    'email' => null,
                    'password' => \Illuminate\Support\Facades\Hash::make($request->password),
                    'role' => 'warga',
                ]);

                if (isset($kk) && $kk && !$kk->user_id) {
                    $kk->update(['user_id' => $newUser->id]);
                }
            }
        }

        return redirect()->route('admin.warga.index')->with('success', 'Data warga berhasil ditambahkan.');
    }

    /**
     * Perbarui Data Warga
     */
    public function update(Request $request, $id)
    {
        $warga = Warga::findOrFail($id);

        $request->merge([
            'nama_lengkap' => $request->input('nama_lengkap') ?: $request->input('name'),
            'no_kk' => $request->input('no_kk') ?: $request->input('nomor_kk'),
            'no_hp' => $request->input('no_hp') ?: $request->input('no_whatsapp'),
        ]);

        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'nik' => 'nullable|string|max:20|unique:wargas,nik,' . $warga->id,
            'no_kk' => 'nullable|string|digits:16',
            'status_keluarga' => 'required|string|in:Kepala Keluarga,Istri,Anak,Famili Lain',
            'nomor_rumah' => 'required|string|max:100',
            'blok' => 'nullable|string|max:50',
            'no_hp' => 'nullable|string|regex:/^[0-9]+$/|max:13',
            'status_tinggal' => 'required|in:tetap,kontrak',
        ], [
            'nama_lengkap.required' => 'Nama kepala keluarga / lengkap wajib diisi.',
            'no_kk.digits' => 'Nomor KK wajib berjumlah tepat 16 digit angka.',
            'no_hp.regex' => 'Nomor WhatsApp hanya boleh berisi angka.',
            'no_hp.max' => 'Nomor WhatsApp maksimal 13 digit angka.',
        ]);

        if (empty($validated['blok']) && preg_match('/(Blok\s+[A-Za-z0-9]+)/i', $validated['nomor_rumah'], $match)) {
            $validated['blok'] = ucwords(strtolower($match[1]));
        }

        if (!empty($validated['no_kk'])) {
            $kk = KartuKeluarga::where('nomor_kk', $validated['no_kk'])->first();
            if ($kk) {
                $validated['kartu_keluarga_id'] = $kk->id;
            }
        }

        $warga->update($validated);

        return redirect()->route('admin.warga.index')->with('success', 'Data warga berhasil diperbarui.');
    }

    /**
     * Hapus Data Warga
     */
    public function destroy($id)
    {
        $warga = Warga::findOrFail($id);
        $warga->delete();

        return redirect()->route('admin.warga.index')->with('success', 'Data warga berhasil dihapus.');
    }

    /**
     * Ambil data repositori berkas keluarga sesuai No. KK warga (AJAX/JSON)
     */
    public function berkas($id)
    {
        $warga = Warga::with('kartuKeluarga')->findOrFail($id);
        
        // Cari berkas keluarga berdasarkan nomor KK terlebih dahulu
        $kk = null;
        if (!empty($warga->no_kk)) {
            $kk = KartuKeluarga::where('nomor_kk', $warga->no_kk)->first();
        }

        // Jika belum ketemu, cari via relasi kartu_keluarga_id
        if (!$kk && $warga->kartu_keluarga_id) {
            $kk = $warga->kartuKeluarga ?: KartuKeluarga::find($warga->kartu_keluarga_id);
        }

        // Jika belum ketemu, fallback ke getBerkasKkAttribute
        if (!$kk) {
            $kk = $warga->berkas_kk;
        }

        // Jika KK ditemukan tapi warga belum terhubung kartu_keluarga_id, tautkan secara otomatis
        if ($kk && !$warga->kartu_keluarga_id) {
            $warga->update(['kartu_keluarga_id' => $kk->id]);
        }

        $noKkDisplay = $warga->no_kk ?: ($kk?->nomor_kk ?: '-');

        // Ambil juga daftar anggota keluarga lain yang memiliki No. KK sama jika ada
        $anggotaKeluarga = [];
        if (!empty($noKkDisplay) && $noKkDisplay !== '-') {
            $anggotaKeluarga = Warga::where('no_kk', $noKkDisplay)
                ->where('id', '!=', $warga->id)
                ->select('id', 'nama_lengkap', 'nik', 'status_keluarga')
                ->get();
        }

        return response()->json([
            'warga' => [
                'id' => $warga->id,
                'nama' => $warga->nama_lengkap,
                'nik' => $warga->nik ?: '-',
                'no_kk' => $noKkDisplay,
                'status_keluarga' => $warga->status_keluarga,
                'nomor_rumah' => $warga->nomor_rumah,
                'no_hp' => $warga->no_hp ?: '-',
            ],
            'kk' => $kk ? [
                'id' => $kk->id,
                'nomor_kk' => $kk->nomor_kk,
                'kepala_keluarga' => $kk->kepala_keluarga ?: $warga->nama_lengkap,
                'nik_kepala_keluarga' => $kk->nik_kepala_keluarga ?: ($warga->nik ?: '-'),
                'nomor_rumah' => $kk->nomor_rumah ?: $warga->nomor_rumah,
                'status_verifikasi' => $kk->status_verifikasi,
                'status_label' => $kk->status_label,
                'catatan_admin' => $kk->catatan_admin,
                'foto_kk_url' => $kk->foto_kk_url,
                'foto_ktp_url' => $kk->foto_ktp_url,
                'foto_bpjs_url' => $kk->foto_bpjs_url,
                'foto_lainnya_url' => $kk->foto_lainnya_url,
            ] : null,
            'anggota_keluarga' => $anggotaKeluarga,
        ]);
    }

    /**
     * Unggah / Perbarui Berkas Dokumen KK untuk Warga
     */
    public function uploadBerkas(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak: Halaman tersebut khusus Pengurus RT.');
        }

        $warga = Warga::findOrFail($id);

        $request->validate([
            'foto_kk' => 'nullable|file|mimes:jpeg,png,jpg,pdf,webp|max:5120',
            'foto_ktp' => 'nullable|file|mimes:jpeg,png,jpg,pdf,webp|max:5120',
            'foto_bpjs' => 'nullable|file|mimes:jpeg,png,jpg,pdf,webp|max:5120',
            'foto_lainnya' => 'nullable|file|mimes:jpeg,png,jpg,pdf,webp|max:5120',
        ], [
            'foto_kk.mimes' => 'Format file Foto KK harus berupa JPG, PNG, WEBP, atau PDF.',
            'foto_kk.max' => 'Ukuran file Foto KK maksimal 5 MB.',
            'foto_ktp.mimes' => 'Format file Foto KTP harus berupa JPG, PNG, WEBP, atau PDF.',
            'foto_ktp.max' => 'Ukuran file Foto KTP maksimal 5 MB.',
            'foto_bpjs.mimes' => 'Format file Foto BPJS harus berupa JPG, PNG, WEBP, atau PDF.',
            'foto_bpjs.max' => 'Ukuran file Foto BPJS maksimal 5 MB.',
            'foto_lainnya.mimes' => 'Format file Dokumen Pendukung harus berupa JPG, PNG, WEBP, atau PDF.',
            'foto_lainnya.max' => 'Ukuran file Dokumen Pendukung maksimal 5 MB.',
        ]);

        // Cari atau buat record KartuKeluarga
        $kk = null;
        if (!empty($warga->no_kk)) {
            $kk = KartuKeluarga::where('nomor_kk', $warga->no_kk)->first();
        }
        if (!$kk && $warga->kartu_keluarga_id) {
            $kk = KartuKeluarga::find($warga->kartu_keluarga_id);
        }

        if (!$kk) {
            $nomorKk = !empty($warga->no_kk) ? $warga->no_kk : 'KK-' . date('YmdHis');
            $kk = KartuKeluarga::create([
                'warga_id' => $warga->id,
                'nomor_kk' => $nomorKk,
                'kepala_keluarga' => $warga->status_keluarga === 'Kepala Keluarga' ? $warga->nama_lengkap : 'Keluarga ' . $warga->nama_lengkap,
                'nik_kepala_keluarga' => $warga->nik,
                'nomor_rumah' => $warga->nomor_rumah,
                'no_hp' => $warga->no_hp,
                'status_verifikasi' => 'terverifikasi',
            ]);

            $warga->update([
                'kartu_keluarga_id' => $kk->id,
                'no_kk' => $nomorKk,
            ]);
        }

        $uploaded = [];
        foreach (['foto_kk', 'foto_ktp', 'foto_bpjs', 'foto_lainnya'] as $field) {
            if ($request->hasFile($field)) {
                if ($kk->$field && Storage::disk('public')->exists($kk->$field)) {
                    Storage::disk('public')->delete($kk->$field);
                }
                $uploaded[$field] = $request->file($field)->store('dokumen_kk', 'public');
            }
        }

        if (!empty($uploaded)) {
            $kk->update($uploaded);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Berkas keluarga berhasil diperbarui.',
                'kk' => $kk->fresh(),
            ]);
        }

        return redirect()->route('admin.warga.index')->with('success', 'Berkas keluarga berhasil diperbarui.');
    }
}
