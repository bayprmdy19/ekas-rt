<?php

namespace App\Http\Controllers;

use App\Models\KartuKeluarga;
use App\Models\Warga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class KartuKeluargaController extends Controller
{
    /**
     * Tampilkan Halaman Utama Data KK untuk Admin RT
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak: Halaman tersebut khusus Pengurus RT.');
        }

        $query = KartuKeluarga::with(['user', 'warga'])->latest();

        if ($request->filled('q')) {
            $q = trim($request->q);
            $query->where(function ($sub) use ($q) {
                $sub->where('kepala_keluarga', 'like', "%{$q}%")
                    ->orWhere('nomor_kk', 'like', "%{$q}%")
                    ->orWhere('nik_kepala_keluarga', 'like', "%{$q}%")
                    ->orWhere('nomor_rumah', 'like', "%{$q}%");
            });
        }

        if ($request->filled('status') && in_array($request->status, ['menunggu', 'terverifikasi', 'ditolak'])) {
            $query->where('status_verifikasi', $request->status);
        }

        $kartuKeluargas = $query->get();

        // Statistik Metrik
        $allKK = KartuKeluarga::all();
        $totalKK = $allKK->count();
        $totalDokumenLengkap = $allKK->filter(fn($item) => $item->isLengkap())->count();
        $totalMenunggu = $allKK->where('status_verifikasi', 'menunggu')->count();
        $totalTerverifikasi = $allKK->where('status_verifikasi', 'terverifikasi')->count();
        $totalDitolak = $allKK->where('status_verifikasi', 'ditolak')->count();

        // Data warga untuk dropdown tambah KK
        $wargaList = Warga::orderBy('nama_lengkap')->get();

        return view('admin.kk.index', compact(
            'kartuKeluargas',
            'totalKK',
            'totalDokumenLengkap',
            'totalMenunggu',
            'totalTerverifikasi',
            'totalDitolak',
            'wargaList'
        ));
    }

    /**
     * Ambil data detail KK untuk preview modal (AJAX/JSON)
     */
    public function show($id)
    {
        $kk = KartuKeluarga::with(['user', 'warga'])->findOrFail($id);

        return response()->json([
            'id' => $kk->id,
            'nomor_kk' => $kk->nomor_kk ?: '-',
            'kepala_keluarga' => $kk->kepala_keluarga,
            'nik_kepala_keluarga' => $kk->nik_kepala_keluarga ?: '-',
            'nomor_rumah' => $kk->nomor_rumah,
            'rt' => $kk->rt ?: '01',
            'rw' => $kk->rw ?: '01',
            'no_hp' => $kk->no_hp ?: '-',
            'jumlah_anggota' => $kk->jumlah_anggota ?: 1,
            'status_verifikasi' => $kk->status_verifikasi,
            'status_label' => $kk->status_label,
            'catatan_admin' => $kk->catatan_admin ?: '',
            'foto_kk_url' => $kk->foto_kk_url,
            'foto_ktp_url' => $kk->foto_ktp_url,
            'foto_bpjs_url' => $kk->foto_bpjs_url,
            'foto_lainnya_url' => $kk->foto_lainnya_url,
            'is_lengkap' => $kk->isLengkap(),
            'created_at_formatted' => $kk->created_at ? $kk->created_at->translatedFormat('d F Y, H:i') . ' WIB' : '-',
            'updated_at_formatted' => $kk->updated_at ? $kk->updated_at->translatedFormat('d F Y, H:i') . ' WIB' : '-',
        ]);
    }

    /**
     * Tambah data KK manual dari sisi Admin
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kepala_keluarga' => 'required|string|max:255',
            'nomor_kk' => 'nullable|string|max:20',
            'nik_kepala_keluarga' => 'nullable|string|max:20',
            'nomor_rumah' => 'required|string|max:50',
            'rt' => 'nullable|string|max:5',
            'rw' => 'nullable|string|max:5',
            'no_hp' => 'nullable|string|max:20',
            'jumlah_anggota' => 'nullable|integer|min:1',
            'status_verifikasi' => 'nullable|in:menunggu,terverifikasi,ditolak',
            'catatan_admin' => 'nullable|string',
            'foto_kk' => 'nullable|file|mimes:jpeg,png,jpg,webp,svg,pdf|max:5120',
            'foto_ktp' => 'nullable|file|mimes:jpeg,png,jpg,webp,svg,pdf|max:5120',
            'foto_bpjs' => 'nullable|file|mimes:jpeg,png,jpg,webp,svg,pdf|max:5120',
            'foto_lainnya' => 'nullable|file|mimes:jpeg,png,jpg,webp,svg,pdf|max:5120',
        ]);

        $data = $validated;
        $data['rt'] = $validated['rt'] ?? '01';
        $data['rw'] = $validated['rw'] ?? '01';
        $data['jumlah_anggota'] = $validated['jumlah_anggota'] ?? 1;
        $data['status_verifikasi'] = $validated['status_verifikasi'] ?? 'terverifikasi';

        // Hubungkan ke master warga jika nama cocok
        $matchingWarga = Warga::where('nama_lengkap', $data['kepala_keluarga'])->first();
        if ($matchingWarga) {
            $data['warga_id'] = $matchingWarga->id;
        }

        // Upload berkas
        if ($request->hasFile('foto_kk')) {
            $data['foto_kk'] = $request->file('foto_kk')->store('dokumen_kk', 'public');
        }
        if ($request->hasFile('foto_ktp')) {
            $data['foto_ktp'] = $request->file('foto_ktp')->store('dokumen_kk', 'public');
        }
        if ($request->hasFile('foto_bpjs')) {
            $data['foto_bpjs'] = $request->file('foto_bpjs')->store('dokumen_kk', 'public');
        }
        if ($request->hasFile('foto_lainnya')) {
            $data['foto_lainnya'] = $request->file('foto_lainnya')->store('dokumen_kk', 'public');
        }

        KartuKeluarga::create($data);

        return redirect()->route('admin.kk.index')->with('success', 'Data KK berhasil ditambahkan.');
    }

    /**
     * Perbarui data KK dari admin
     */
    public function update(Request $request, $id)
    {
        $kk = KartuKeluarga::findOrFail($id);

        $validated = $request->validate([
            'kepala_keluarga' => 'required|string|max:255',
            'nomor_kk' => 'nullable|string|max:20',
            'nik_kepala_keluarga' => 'nullable|string|max:20',
            'nomor_rumah' => 'required|string|max:50',
            'rt' => 'nullable|string|max:5',
            'rw' => 'nullable|string|max:5',
            'no_hp' => 'nullable|string|max:20',
            'jumlah_anggota' => 'nullable|integer|min:1',
            'status_verifikasi' => 'nullable|in:menunggu,terverifikasi,ditolak',
            'catatan_admin' => 'nullable|string',
            'foto_kk' => 'nullable|file|mimes:jpeg,png,jpg,webp,svg,pdf|max:5120',
            'foto_ktp' => 'nullable|file|mimes:jpeg,png,jpg,webp,svg,pdf|max:5120',
            'foto_bpjs' => 'nullable|file|mimes:jpeg,png,jpg,webp,svg,pdf|max:5120',
            'foto_lainnya' => 'nullable|file|mimes:jpeg,png,jpg,webp,svg,pdf|max:5120',
        ]);

        $data = $validated;

        if ($request->hasFile('foto_kk')) {
            if ($kk->foto_kk) Storage::disk('public')->delete($kk->foto_kk);
            $data['foto_kk'] = $request->file('foto_kk')->store('dokumen_kk', 'public');
        }
        if ($request->hasFile('foto_ktp')) {
            if ($kk->foto_ktp) Storage::disk('public')->delete($kk->foto_ktp);
            $data['foto_ktp'] = $request->file('foto_ktp')->store('dokumen_kk', 'public');
        }
        if ($request->hasFile('foto_bpjs')) {
            if ($kk->foto_bpjs) Storage::disk('public')->delete($kk->foto_bpjs);
            $data['foto_bpjs'] = $request->file('foto_bpjs')->store('dokumen_kk', 'public');
        }
        if ($request->hasFile('foto_lainnya')) {
            if ($kk->foto_lainnya) Storage::disk('public')->delete($kk->foto_lainnya);
            $data['foto_lainnya'] = $request->file('foto_lainnya')->store('dokumen_kk', 'public');
        }

        $kk->update($data);

        return redirect()->route('admin.kk.index')->with('success', 'Data KK berhasil diperbarui.');
    }

    /**
     * Ubah status verifikasi KK (Verifikasi / Tolak)
     */
    public function updateStatus(Request $request, $id)
    {
        $kk = KartuKeluarga::findOrFail($id);

        $validated = $request->validate([
            'status_verifikasi' => 'required|in:menunggu,terverifikasi,ditolak',
            'catatan_admin' => 'nullable|string',
        ]);

        $kk->update([
            'status_verifikasi' => $validated['status_verifikasi'],
            'catatan_admin' => $validated['catatan_admin'] ?? $kk->catatan_admin,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Status verifikasi data KK berhasil diubah.',
                'status' => $kk->status_verifikasi,
                'status_label' => $kk->status_label,
            ]);
        }

        return redirect()->back()->with('success', 'Status verifikasi data KK berhasil diubah.');
    }

    /**
     * Hapus data KK
     */
    public function destroy($id)
    {
        $kk = KartuKeluarga::findOrFail($id);

        if ($kk->foto_kk) Storage::disk('public')->delete($kk->foto_kk);
        if ($kk->foto_ktp) Storage::disk('public')->delete($kk->foto_ktp);
        if ($kk->foto_bpjs) Storage::disk('public')->delete($kk->foto_bpjs);
        if ($kk->foto_lainnya) Storage::disk('public')->delete($kk->foto_lainnya);

        $kk->delete();

        return redirect()->route('admin.kk.index')->with('success', 'Data KK berhasil dihapus.');
    }
}
