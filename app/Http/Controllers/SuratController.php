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

        if ($request->wantsJson() || $request->ajax()) {
            $formattedSurats = $surats->map(function ($item) {
                $warga = Warga::where('nama_lengkap', $item->nama_pengaju)->first();
                return [
                    'id' => $item->id,
                    'nomor_surat' => $item->nomor_surat ?: $item->nomor_surat_display,
                    'nama_pengaju' => $item->nama_pengaju,
                    'nomor_rumah' => $item->nomor_rumah,
                    'jenis_surat' => $item->jenis_surat,
                    'keperluan' => $item->keperluan,
                    'tanggal' => $item->created_at ? $item->created_at->format('Y-m-d') : '-',
                    'tanggal_formatted' => $item->created_at ? $item->created_at->translatedFormat('d M Y') : '-',
                    'status' => $item->status,
                    'status_label' => $item->status_label,
                    'catatan' => $item->catatan ?: '-',
                    'cetak_url' => route('admin.surat.cetak', $item->id),
                    'nik_pemohon' => $warga?->nik ?: '-',
                    'no_hp_pemohon' => $warga?->no_hp ?: '',
                    'blok_pemohon' => $warga?->blok ?: ($item->nomor_rumah ?: '-'),
                    'status_tinggal' => $warga?->status_tinggal ?: 'tetap',
                ];
            });

            return response()->json([
                'success' => true,
                'surats' => $formattedSurats,
                'totalSurat' => $totalSurat,
                'totalMenunggu' => $totalMenunggu,
                'totalDisetujui' => $totalDisetujui,
                'totalDitolak' => $totalDitolak,
            ]);
        }

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
     * Kirim Surat Resmi ke Pemohon & Sinkronkan ke Portal Warga
     */
    public function kirim(Request $request, $id)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $surat = Administrasi::findOrFail($id);

        $request->validate([
            'no_hp' => 'nullable|string|max:25',
            'pesan' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        // Otomatis ubah status ke 'disetujui' jika sebelumnya masih menunggu
        if ($surat->status !== 'disetujui') {
            $surat->status = 'disetujui';
            if (empty($surat->nomor_surat)) {
                $surat->nomor_surat = $surat->nomor_surat_display;
            }
        }

        $catatanKirim = 'Surat telah dikirimkan ke pemohon pada ' . Carbon::now()->translatedFormat('d F Y, H:i') . ' WIB';
        $surat->catatan = $request->filled('catatan') ? $request->catatan : $catatanKirim;
        $surat->save();

        // Sinkronkan ke model Pengaduan agar akun pemohon di Portal Warga langsung mendapat status 'selesai' dan tombol download surat
        $aduan = Pengaduan::where('judul_laporan', $surat->jenis_surat)
            ->where('nomor_rumah', $surat->nomor_rumah)
            ->first();

        if ($aduan) {
            $aduan->update([
                'status' => 'selesai',
                'selesai_at' => Carbon::now(),
            ]);
        } else {
            Pengaduan::create([
                'nomor_rumah' => $surat->nomor_rumah,
                'nama_pelapor' => $surat->nama_pengaju,
                'kategori' => 'Administrasi Surat',
                'judul_laporan' => $surat->jenis_surat,
                'isi_laporan' => $surat->keperluan,
                'status' => 'selesai',
                'selesai_at' => Carbon::now(),
            ]);
        }

        $warga = Warga::where('nama_lengkap', $surat->nama_pengaju)
            ->orWhere('nomor_rumah', $surat->nomor_rumah)
            ->first();

        $phone = $request->no_hp ?: ($warga?->no_hp ?: '');
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '62' . substr($cleanPhone, 1);
        }

        $cetakUrl = route('admin.surat.cetak', $surat->id);
        $pesanTeks = $request->pesan ?: "Yth. {$surat->nama_pengaju},\n\nPermohonan {$surat->jenis_surat} Anda (Nomor: {$surat->nomor_surat}) telah selesai diterbitkan dan disetujui oleh Pengurus RT 01.\n\nKeperluan: {$surat->keperluan}\nDokumen resmi dapat dilihat dan diunduh melalui tautan berikut:\n{$cetakUrl}\n\nTerima kasih.\nPengurus RT 01 RW 06";

        $waUrl = !empty($cleanPhone) ? "https://wa.me/{$cleanPhone}?text=" . urlencode($pesanTeks) : null;

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Surat \"{$surat->jenis_surat}\" berhasil dikirim ke pemohon ({$surat->nama_pengaju}).",
                'surat' => [
                    'id' => $surat->id,
                    'status' => $surat->status,
                    'status_label' => $surat->status_label,
                    'catatan' => $surat->catatan,
                    'nomor_surat' => $surat->nomor_surat,
                ],
                'whatsapp_url' => $waUrl,
            ]);
        }

        return redirect()->route('admin.surat.index')->with('success', "Surat \"{$surat->jenis_surat}\" berhasil dikirim ke pemohon.");
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
