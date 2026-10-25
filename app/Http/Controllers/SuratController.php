<?php

namespace App\Http\Controllers;

use App\Models\Administrasi;
use App\Models\Pengaduan;
use App\Models\Warga;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuratController extends Controller
{
    /**
     * Tampilkan Halaman Manajemen Pengajuan Surat untuk Admin RT
     */
    public function index(Request $request)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return redirect()->route('warga.dashboard')->with('error', 'Akses khusus untuk Pengurus RT.');
        }

        $surats = Administrasi::orderBy('created_at', 'desc')->get();

        // Ringkasan data metrik
        $totalSurat = $surats->count();
        $totalMenunggu = $surats->filter(fn($s) => in_array(strtolower($s->status), ['menunggu', 'pending']))->count();
        $totalDisetujui = $surats->filter(fn($s) => in_array(strtolower($s->status), ['disetujui', 'selesai']))->count();
        $totalDitolak = $surats->filter(fn($s) => strtolower($s->status) === 'ditolak')->count();

        // Data warga untuk dropdown pembuatan surat manual
        $wargaList = Warga::orderBy('nama_lengkap')->get();

        return view('admin.surat.index', compact(
            'surats',
            'totalSurat',
            'totalMenunggu',
            'totalDisetujui',
            'totalDitolak',
            'wargaList'
        ));
    }

    /**
     * Tambah Pengajuan Surat Baru oleh Admin
     */
    public function store(Request $request)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $validated = $request->validate([
            'nama_pengaju' => 'required|string|max:100',
            'nomor_rumah' => 'required|string|max:50',
            'jenis_surat' => 'required|string|max:100',
            'keperluan' => 'required|string',
            'nomor_surat' => 'nullable|string|max:100',
            'catatan' => 'nullable|string',
            'status' => 'required|in:menunggu,pending,disetujui,ditolak',
        ]);

        $status = in_array($validated['status'], ['pending', 'menunggu']) ? 'menunggu' : $validated['status'];

        $surat = Administrasi::create([
            'nama_pengaju' => $validated['nama_pengaju'],
            'nomor_rumah' => $validated['nomor_rumah'],
            'jenis_surat' => $validated['jenis_surat'],
            'keperluan' => $validated['keperluan'],
            'nomor_surat' => $validated['nomor_surat'] ?: null,
            'catatan' => $validated['catatan'] ?: '-',
            'status' => $status,
        ]);

        // Jika nomor surat belum diisi, generate otomatis
        if (empty($surat->nomor_surat)) {
            $surat->nomor_surat = $surat->nomor_surat_display;
            $surat->save();
        }

        // Sinkronkan ke tabel Pengaduan agar Portal Warga juga terhubung
        Pengaduan::create([
            'nomor_rumah' => $surat->nomor_rumah,
            'nama_pelapor' => $surat->nama_pengaju,
            'kategori' => 'Administrasi Surat',
            'judul_laporan' => $surat->jenis_surat,
            'isi_laporan' => $surat->keperluan,
            'status' => $status === 'disetujui' ? 'selesai' : 'diterima',
            'selesai_at' => $status === 'disetujui' ? Carbon::now() : null,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Pengajuan surat berhasil ditambahkan.',
                'surat' => $surat
            ]);
        }

        return redirect()->route('admin.surat.index')->with('success', 'Pengajuan surat berhasil ditambahkan.');
    }

    /**
     * Perbarui Data Pengajuan Surat
     */
    public function update(Request $request, $id)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $validated = $request->validate([
            'nama_pengaju' => 'required|string|max:100',
            'nomor_rumah' => 'required|string|max:50',
            'jenis_surat' => 'required|string|max:100',
            'keperluan' => 'required|string',
            'nomor_surat' => 'nullable|string|max:100',
            'catatan' => 'nullable|string',
            'status' => 'required|in:menunggu,pending,disetujui,ditolak',
        ]);

        $surat = Administrasi::findOrFail($id);
        $status = in_array($validated['status'], ['pending', 'menunggu']) ? 'menunggu' : $validated['status'];

        $surat->update([
            'nama_pengaju' => $validated['nama_pengaju'],
            'nomor_rumah' => $validated['nomor_rumah'],
            'jenis_surat' => $validated['jenis_surat'],
            'keperluan' => $validated['keperluan'],
            'nomor_surat' => $validated['nomor_surat'] ?: $surat->nomor_surat_display,
            'catatan' => $validated['catatan'] ?: '-',
            'status' => $status,
        ]);

        // Sinkronkan ke tabel Pengaduan warga
        Pengaduan::where('judul_laporan', $surat->jenis_surat)
            ->where('nomor_rumah', $surat->nomor_rumah)
            ->update([
                'status' => $status === 'disetujui' ? 'selesai' : 'diterima',
                'selesai_at' => $status === 'disetujui' ? Carbon::now() : null,
            ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Data surat {$surat->nomor_surat} berhasil diperbarui.",
                'surat' => $surat
            ]);
        }

        return redirect()->route('admin.surat.index')->with('success', "Data surat berhasil diperbarui.");
    }

    /**
     * Ubah Status Pengajuan Surat (Menunggu, Disetujui, Ditolak)
     */
    public function updateStatus(Request $request, $id)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:menunggu,pending,disetujui,ditolak',
            'catatan' => 'nullable|string',
            'nomor_surat' => 'nullable|string|max:100',
        ]);

        $surat = Administrasi::findOrFail($id);
        $status = in_array($validated['status'], ['pending', 'menunggu']) ? 'menunggu' : $validated['status'];

        if (!empty($validated['nomor_surat'])) {
            $surat->nomor_surat = $validated['nomor_surat'];
        } elseif (empty($surat->nomor_surat)) {
            $surat->nomor_surat = $surat->nomor_surat_display;
        }

        $surat->status = $status;
        if (isset($validated['catatan'])) {
            $surat->catatan = $validated['catatan'];
        }
        $surat->save();

        // Sinkronkan ke Pengaduan warga: jika disetujui, status di portal warga otomatis jadi 'selesai'
        Pengaduan::where('judul_laporan', $surat->jenis_surat)
            ->where('nomor_rumah', $surat->nomor_rumah)
            ->update([
                'status' => $status === 'disetujui' ? 'selesai' : ($status === 'ditolak' ? 'diproses' : 'diterima'),
                'selesai_at' => $status === 'disetujui' ? Carbon::now() : null,
            ]);

        $msg = "Status surat \"{$surat->jenis_surat}\" ({$surat->nama_pengaju}) berhasil diperbarui menjadi {$surat->status_label}.";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
                'surat' => [
                    'id' => $surat->id,
                    'nomor_surat' => $surat->nomor_surat,
                    'status' => $surat->status,
                    'status_label' => $surat->status_label,
                    'catatan' => $surat->catatan ?: '-',
                ]
            ]);
        }

        return redirect()->route('admin.surat.index')->with('success', $msg);
    }

    /**
     * Hapus Pengajuan Surat
     */
    public function destroy($id)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $surat = Administrasi::findOrFail($id);
        $jenis = $surat->jenis_surat;
        $surat->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Surat \"{$jenis}\" berhasil dihapus."
            ]);
        }

        return redirect()->route('admin.surat.index')->with('success', "Surat \"{$jenis}\" berhasil dihapus.");
    }

    /**
     * Tampilan Cetak / Unduh Surat Resmi RT oleh Admin
     */
    public function cetak($id)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return redirect()->route('warga.dashboard')->with('error', 'Akses ditolak.');
        }

        $surat = Administrasi::findOrFail($id);
        $wargaProfile = Warga::where('nama_lengkap', $surat->nama_pengaju)->first();

        // Buat objek mock/wrapper yang kompatibel dengan template cetak.blade.php
        $pengaduan = (object)[
            'id' => $surat->id,
            'nama_pelapor' => $surat->nama_pengaju,
            'nomor_rumah' => $surat->nomor_rumah,
            'judul_laporan' => $surat->jenis_surat,
            'isi_laporan' => $surat->keperluan,
            'selesai_at' => $surat->updated_at,
        ];

        $nomorSurat = $surat->nomor_surat ?: $surat->nomor_surat_display;
        $user = Auth::user();

        return view('warga.surat.cetak', compact(
            'pengaduan',
            'nomorSurat',
            'wargaProfile',
            'user'
        ));
    }
}
