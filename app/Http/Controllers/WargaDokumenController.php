<?php

namespace App\Http\Controllers;

use App\Models\KartuKeluarga;
use App\Models\Warga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class WargaDokumenController extends Controller
{
    /**
     * Tampilkan formulir kelengkapan data diri & dokumen KK untuk Warga
     */
    public function index()
    {
        $user = Auth::user();

        // Cari data KK berdasarkan user_id, atau kepala keluarga nama yang sama
        $kk = KartuKeluarga::where('user_id', $user->id)
            ->orWhere('kepala_keluarga', $user->name)
            ->first();

        // Cari profile Warga yang sudah ada
        $wargaProfile = Warga::where('nama_lengkap', $user->name)->first();

        return view('warga.dokumen.index', compact('user', 'kk', 'wargaProfile'));
    }

    /**
     * Simpan atau perbarui data diri & upload dokumen foto KK, KTP, BPJS
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'nomor_kk' => 'nullable|string|max:20',
            'nik_kepala_keluarga' => 'nullable|string|max:20',
            'nomor_rumah' => 'required|string|max:50',
            'rt' => 'nullable|string|max:5',
            'rw' => 'nullable|string|max:5',
            'no_hp' => 'nullable|string|max:20',
            'jumlah_anggota' => 'nullable|integer|min:1',
            'foto_kk' => 'nullable|file|mimes:jpeg,png,jpg,webp,svg,pdf|max:5120',
            'foto_ktp' => 'nullable|file|mimes:jpeg,png,jpg,webp,svg,pdf|max:5120',
            'foto_bpjs' => 'nullable|file|mimes:jpeg,png,jpg,webp,svg,pdf|max:5120',
            'foto_lainnya' => 'nullable|file|mimes:jpeg,png,jpg,webp,svg,pdf|max:5120',
        ], [
            'nomor_rumah.required' => 'Nomor rumah / blok wajib diisi.',
            'foto_kk.mimes' => 'Format foto KK harus berupa gambar (JPG, PNG, WEBP) atau PDF.',
            'foto_ktp.mimes' => 'Format foto KTP harus berupa gambar (JPG, PNG, WEBP) atau PDF.',
            'foto_bpjs.mimes' => 'Format foto BPJS harus berupa gambar (JPG, PNG, WEBP) atau PDF.',
            'foto_kk.max' => 'Ukuran file foto KK maksimal 5MB.',
            'foto_ktp.max' => 'Ukuran file foto KTP maksimal 5MB.',
            'foto_bpjs.max' => 'Ukuran file foto BPJS maksimal 5MB.',
        ]);

        $kk = KartuKeluarga::where('user_id', $user->id)
            ->orWhere('kepala_keluarga', $user->name)
            ->first();

        $wargaProfile = Warga::where('nama_lengkap', $user->name)->first();

        $data = [
            'user_id' => $user->id,
            'warga_id' => $wargaProfile?->id,
            'kepala_keluarga' => $user->name,
            'nomor_kk' => $validated['nomor_kk'] ?? $kk?->nomor_kk,
            'nik_kepala_keluarga' => $validated['nik_kepala_keluarga'] ?? $kk?->nik_kepala_keluarga,
            'nomor_rumah' => $validated['nomor_rumah'],
            'rt' => $validated['rt'] ?? ($kk?->rt ?? '01'),
            'rw' => $validated['rw'] ?? ($kk?->rw ?? '01'),
            'no_hp' => $validated['no_hp'] ?? $kk?->no_hp,
            'jumlah_anggota' => $validated['jumlah_anggota'] ?? ($kk?->jumlah_anggota ?? 1),
            // Jika warga baru mengunggah/mengedit berkas, jadikan status menunggu untuk ditinjau pengurus RT
            'status_verifikasi' => 'menunggu',
        ];

        // Handle upload berkas
        if ($request->hasFile('foto_kk')) {
            if ($kk && $kk->foto_kk) Storage::disk('public')->delete($kk->foto_kk);
            $data['foto_kk'] = $request->file('foto_kk')->store('dokumen_kk', 'public');
        }

        if ($request->hasFile('foto_ktp')) {
            if ($kk && $kk->foto_ktp) Storage::disk('public')->delete($kk->foto_ktp);
            $data['foto_ktp'] = $request->file('foto_ktp')->store('dokumen_kk', 'public');
        }

        if ($request->hasFile('foto_bpjs')) {
            if ($kk && $kk->foto_bpjs) Storage::disk('public')->delete($kk->foto_bpjs);
            $data['foto_bpjs'] = $request->file('foto_bpjs')->store('dokumen_kk', 'public');
        }

        if ($request->hasFile('foto_lainnya')) {
            if ($kk && $kk->foto_lainnya) Storage::disk('public')->delete($kk->foto_lainnya);
            $data['foto_lainnya'] = $request->file('foto_lainnya')->store('dokumen_kk', 'public');
        }

        if ($kk) {
            $kk->update($data);
        } else {
            $kk = KartuKeluarga::create($data);
        }

        // Sinkronisasi data warga jika sudah ada record di tabel wargas
        if ($wargaProfile) {
            $wargaProfile->update([
                'nomor_rumah' => $data['nomor_rumah'],
                'no_hp' => $data['no_hp'] ?: $wargaProfile->no_hp,
                'nik' => $data['nik_kepala_keluarga'] ?: $wargaProfile->nik,
            ]);
        }

        return redirect()->back()->with('success', 'Data diri dan foto dokumen KK, KTP, serta BPJS berhasil disimpan dan diteruskan ke Pengurus RT untuk verifikasi.');
    }
}
