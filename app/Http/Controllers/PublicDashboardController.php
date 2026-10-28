<?php

namespace App\Http\Controllers;

use App\Models\KasRt;
use App\Models\IuranWarga;
use App\Models\Pengaduan;
use App\Models\Administrasi;
use App\Models\Warga;
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
        $user = auth()->user();
        $isLainLain = $request->input('kategori_layanan') === 'Lain-lain';

        // 1. Validasi formulir pengajuan
        $rules = [
            'kategori_layanan' => 'required|string',
            'nomor_rumah' => $user ? 'nullable|string|max:50' : 'required|string|max:50',
            'nama_pemohon' => $user ? 'nullable|string|max:100' : 'required|string|max:100',
            'nomor_whatsapp' => $user ? 'nullable|string|max:20' : 'required|string|max:20',
            'deskripsi' => $isLainLain ? 'nullable|string' : 'required|string',
        ];

        // Validasi wajib untuk opsi "Lain-lain"
        if ($isLainLain) {
            $rules['tujuan_surat'] = 'required|string|max:255';
            $rules['keterangan_detail'] = 'required|string';
        }

        $validated = $request->validate($rules, [
            'kategori_layanan.required' => 'Kategori layanan wajib dipilih.',
            'nomor_rumah.required' => 'Nomor rumah / blok wajib diisi.',
            'nama_pemohon.required' => 'Nama pemohon wajib diisi.',
            'nomor_whatsapp.required' => 'Nomor WhatsApp wajib diisi.',
            'deskripsi.required' => 'Deskripsi laporan / keperluan wajib diisi.',
            'tujuan_surat.required' => 'Tujuan surat wajib diisi untuk kategori Lain-lain.',
            'keterangan_detail.required' => 'Keterangan detail wajib diisi untuk kategori Lain-lain.',
        ]);

        // 2. Tentukan data profil pemohon berdasarkan status Login (Auth / Guest)
        $warga = null;
        if ($user) {
            $warga = $user->warga ?: Warga::where('user_id', $user->id)
                ->orWhere('no_hp', $user->no_hp)
                ->orWhere('nama_lengkap', $user->name)
                ->first();

            $nama = $user->name;
            $nomorRumah = $warga?->nomor_rumah ?: ($user->kartuKeluarga?->nomor_rumah ?: ($request->nomor_rumah ?: '-'));
            $nomorWa = $user->no_hp ?: ($warga?->no_hp ?: ($request->nomor_whatsapp ?: ''));
            $wargaId = $warga?->id;
            $userId = $user->id;
        } else {
            // Jika Guest, cari apakah nomor WhatsApp atau nama cocok dengan data warga terdaftar
            $warga = Warga::where('no_hp', $request->nomor_whatsapp)
                ->orWhere('nama_lengkap', $request->nama_pemohon)
                ->first();

            $nama = !empty(trim($request->nama_pemohon ?? '')) ? trim($request->nama_pemohon) : 'Warga (Anonim)';
            $nomorRumah = $request->nomor_rumah;
            $nomorWa = $request->nomor_whatsapp ?: ($warga?->no_hp ?: '');
            $wargaId = $warga?->id;
            $userId = $warga?->user_id;
        }

        // 3. Tangani opsi "Lain-lain"
        if ($isLainLain) {
            $tujuanSurat = $validated['tujuan_surat'];
            $keteranganDetail = $validated['keterangan_detail'];
            $deskripsi = !empty(trim($request->deskripsi ?? '')) ? trim($request->deskripsi) : ($tujuanSurat . ' - ' . $keteranganDetail);
        } else {
            $tujuanSurat = null;
            $keteranganDetail = null;
            $deskripsi = $validated['deskripsi'];
        }

        $isSurat = $isLainLain ||
                   str_contains($validated['kategori_layanan'], 'Surat') || 
                   str_contains($validated['kategori_layanan'], 'KTP') ||
                   str_contains($validated['kategori_layanan'], 'Domisili') ||
                   str_contains($validated['kategori_layanan'], 'SKU') ||
                   str_contains($validated['kategori_layanan'], 'Nikah');

        // 4. Simpan ke tabel Pengaduan
        Pengaduan::create([
            'user_id' => $userId,
            'warga_id' => $wargaId,
            'nomor_rumah' => $nomorRumah,
            'nama_pelapor' => $nama,
            'nomor_whatsapp' => $nomorWa,
            'kategori' => $isSurat ? 'Administrasi Surat' : 'Pengaduan Lingkungan',
            'judul_laporan' => $validated['kategori_layanan'],
            'isi_laporan' => $deskripsi,
            'tujuan_surat' => $tujuanSurat,
            'keterangan_detail' => $keteranganDetail,
            'status' => 'diterima',
        ]);

        // 5. Jika permohonan surat, simpan ke tabel Administrasi
        if ($isSurat) {
            $count = Administrasi::count() + 1;
            $kodeSurat = match(true) {
                str_contains($validated['kategori_layanan'], 'Domisili') => 'DOM',
                str_contains($validated['kategori_layanan'], 'SKU') || str_contains($validated['kategori_layanan'], 'Usaha') => 'SKU',
                str_contains($validated['kategori_layanan'], 'Nikah') => 'NA',
                str_contains($validated['kategori_layanan'], 'Kematian') => 'SKM',
                str_contains($validated['kategori_layanan'], 'Kelahiran') => 'SKL',
                $isLainLain => 'LAIN',
                default => 'PRT'
            };
            $noSurat = sprintf("%03d/%s/RT/%s/%s", $count, $kodeSurat, date('m'), date('Y'));

            Administrasi::create([
                'user_id' => $userId,
                'warga_id' => $wargaId,
                'nomor_surat' => $noSurat,
                'nomor_rumah' => $nomorRumah,
                'nama_pengaju' => $nama,
                'nomor_whatsapp' => $nomorWa,
                'jenis_surat' => $validated['kategori_layanan'],
                'keperluan' => $deskripsi,
                'tujuan_surat' => $tujuanSurat,
                'keterangan_detail' => $keteranganDetail,
                'status' => 'menunggu',
            ]);
        }

        $previousUrl = strtok(url()->previous(), '#');
        $successMsg = $isSurat
            ? "Permohonan {$validated['kategori_layanan']} berhasil dikirim! Data telah masuk ke antrean verifikasi Surat Pengurus RT."
            : "Laporan aduan lingkungan berhasil dikirim! Data telah masuk ke antrean Pengaduan Warga Pengurus RT.";

        return redirect()->to($previousUrl . '#layanan-warga')
            ->with('success', $successMsg);
    }
}