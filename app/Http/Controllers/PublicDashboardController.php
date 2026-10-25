<?php

namespace App\Http\Controllers;

use App\Models\KasRt;
use App\Models\IuranWarga;
use App\Models\Pengaduan;
use App\Models\Administrasi;
use Illuminate\Http\Request;

class PublicDashboardController extends Controller
{
    public function index()
    {
        $totalMasuk = KasRt::where('jenis', 'masuk')->sum('jumlah');
        $totalKeluar = KasRt::where('jenis', 'keluar')->sum('jumlah');
        $saldoKas = $totalMasuk - $totalKeluar;

        return view('welcome', compact(
            'totalMasuk',
            'totalKeluar',
            'saldoKas'
        ));
    }

    public function storeLayanan(Request $request)
    {
        $validated = $request->validate([
            'kategori_layanan' => 'required|string',
            'nomor_rumah' => 'required|string|max:50',
            'nama_pemohon' => 'nullable|string|max:100',
            'deskripsi' => 'required|string',
        ]);

        $nama = !empty(trim($request->nama_pemohon ?? '')) ? trim($request->nama_pemohon) : 'Warga (Anonim)';
        
        $isSurat = str_contains($validated['kategori_layanan'], 'Surat') || 
                   str_contains($validated['kategori_layanan'], 'KTP') ||
                   str_contains($validated['kategori_layanan'], 'Domisili') ||
                   str_contains($validated['kategori_layanan'], 'SKU') ||
                   str_contains($validated['kategori_layanan'], 'Nikah');

        // Simpan ke tabel Pengaduan agar selalu muncul di Halaman Pengaduan Admin
        Pengaduan::create([
            'user_id' => auth()->id(),
            'nomor_rumah' => $validated['nomor_rumah'],
            'nama_pelapor' => $nama,
            'kategori' => $isSurat ? 'Administrasi Surat' : 'Pengaduan Lingkungan',
            'judul_laporan' => $validated['kategori_layanan'],
            'isi_laporan' => $validated['deskripsi'],
            'status' => 'diterima',
        ]);

        // Jika merupakan permohonan administrasi surat, simpan juga ke tabel Administrasi
        if ($isSurat) {
            $count = Administrasi::count() + 1;
            $kodeSurat = match(true) {
                str_contains($validated['kategori_layanan'], 'Domisili') => 'DOM',
                str_contains($validated['kategori_layanan'], 'SKU') || str_contains($validated['kategori_layanan'], 'Usaha') => 'SKU',
                str_contains($validated['kategori_layanan'], 'Nikah') => 'NA',
                str_contains($validated['kategori_layanan'], 'Kematian') => 'SKM',
                str_contains($validated['kategori_layanan'], 'Kelahiran') => 'SKL',
                default => 'PRT'
            };
            $noSurat = sprintf("%03d/%s/RT/%s/%s", $count, $kodeSurat, date('m'), date('Y'));

            Administrasi::create([
                'user_id' => auth()->id(),
                'nomor_surat' => $noSurat,
                'nomor_rumah' => $validated['nomor_rumah'],
                'nama_pengaju' => $nama,
                'jenis_surat' => $validated['kategori_layanan'],
                'keperluan' => $validated['deskripsi'],
                'status' => 'menunggu',
            ]);
        }

        $previousUrl = strtok(url()->previous(), '#');
        return redirect()->to($previousUrl . '#layanan-warga')
            ->with('success', 'Pengajuan formulir berhasil dikirim! Data telah masuk ke antrean pengaduan & layanan pengurus RT.');
    }
}