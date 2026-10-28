<?php

namespace App\Http\Controllers;

use App\Models\Administrasi;
use App\Models\IuranWarga;
use App\Models\KasRt;
use App\Models\Pengaduan;
use App\Models\Warga;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    /**
     * Dispatcher route untuk /dashboard
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user && $user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        if ($user && $user->isWarga() && !$user->is_profile_complete) {
            return redirect()->route('warga.onboarding');
        }

        return redirect()->route('warga.dashboard');
    }

    /**
     * Dashboard Pengurus / Admin RT
     */
    public function admin()
    {
        $user = Auth::user();

        if (!$user->isAdmin()) {
            return redirect()->route('warga.dashboard')->with('error', 'Akses ditolak: Halaman tersebut khusus untuk Pengurus RT.');
        }

        // Jalankan evaluasi rating 1x24 jam secara realtime
        Pengaduan::autoResolveExpiredRatings();

        $totalMasuk = KasRt::where('jenis', 'masuk')->sum('jumlah');
        $totalKeluar = KasRt::where('jenis', 'keluar')->sum('jumlah');
        $saldoKas = $totalMasuk - $totalKeluar;

        $totalWarga = Warga::count();
        $totalPengaduan = Pengaduan::count();
        $totalSurat = Administrasi::count();
        $totalArsip = $totalPengaduan + $totalSurat;
        $totalTransaksi = KasRt::count();

        // Metrik tambahan untuk tampilan RT/RW Digital
        $totalKK = Warga::distinct('nomor_rumah')->count('nomor_rumah') ?: 1;
        $totalWargaAktif = $totalWarga > 0 ? $totalWarga : 1;
        $pembayaranBulanIni = IuranWarga::where('status', 'lunas')->sum('jumlah');
        $suratMenunggu = Administrasi::whereIn('status', ['menunggu', 'pending'])->count();
        $pengaduanBaru = Pengaduan::whereIn('status', ['pending', 'diterima'])->where('kategori', '!=', 'Administrasi Surat')->count();
        $kegiatanMendatang = 3;

        $wargaListAll = Warga::orderBy('nama_lengkap')->get();

        $transaksiTerbaru = KasRt::latest('tanggal')->take(6)->get();
        $iuranWarga = IuranWarga::with('warga')->latest()->take(6)->get();
        $pengaduanTerbaru = Pengaduan::latest()->take(6)->get();
        $suratTerbaru = Administrasi::latest()->take(6)->get();
        $wargaTerbaru = Warga::latest()->take(6)->get();

        // Menggabungkan seluruh aktivitas data untuk feed 'Konten Terbaru'
        $kontenTerbaru = collect();

        foreach ($transaksiTerbaru as $kas) {
            $kontenTerbaru->push([
                'id' => 'kas_' . $kas->id,
                'judul' => $kas->kategori,
                'kategori' => 'Kas RT',
                'subkategori' => ucfirst($kas->jenis),
                'keterangan' => $kas->keterangan . ' (Rp ' . number_format($kas->jumlah, 0, ',', '.') . ')',
                'tanggal' => Carbon::parse($kas->tanggal ?? $kas->created_at),
                'tanggal_formatted' => Carbon::parse($kas->tanggal ?? $kas->created_at)->format('d M Y H:i'),
                'type' => 'kas',
                'badge' => $kas->jenis == 'masuk' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600',
                'detail' => [
                    'Tanggal' => Carbon::parse($kas->tanggal)->format('d F Y'),
                    'Jenis' => ucfirst($kas->jenis),
                    'Kategori' => $kas->kategori,
                    'Nominal' => 'Rp ' . number_format($kas->jumlah, 0, ',', '.'),
                    'Keterangan' => $kas->keterangan,
                ],
            ]);
        }

        foreach ($iuranWarga as $iuran) {
            $kontenTerbaru->push([
                'id' => 'iuran_' . $iuran->id,
                'judul' => $iuran->warga ? $iuran->warga->nama_lengkap : 'Warga Telah Dihapus',
                'kategori' => 'Iuran Warga',
                'subkategori' => ucfirst($iuran->jenis_iuran),
                'keterangan' => 'Periode ' . $iuran->bulan_tahun . ' - Rp ' . number_format($iuran->jumlah, 0, ',', '.') . ' (' . strtoupper($iuran->status) . ')',
                'tanggal' => Carbon::parse($iuran->created_at),
                'tanggal_formatted' => Carbon::parse($iuran->created_at)->format('d M Y H:i'),
                'type' => 'iuran',
                'badge' => $iuran->status == 'lunas' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600',
                'detail' => [
                    'Warga' => $iuran->warga ? $iuran->warga->nama_lengkap : 'Warga Telah Dihapus',
                    'Rumah' => $iuran->warga ? $iuran->warga->nomor_rumah : '-',
                    'Jenis Iuran' => ucfirst($iuran->jenis_iuran),
                    'Periode' => $iuran->bulan_tahun,
                    'Nominal' => 'Rp ' . number_format($iuran->jumlah, 0, ',', '.'),
                    'Status' => strtoupper($iuran->status),
                    'Catatan' => $iuran->keterangan ?? '-',
                ],
            ]);
        }

        foreach ($pengaduanTerbaru as $pengaduan) {
            $kontenTerbaru->push([
                'id' => 'pengaduan_' . $pengaduan->id,
                'judul' => $pengaduan->judul_laporan,
                'kategori' => 'Pengaduan',
                'subkategori' => $pengaduan->nomor_rumah,
                'keterangan' => ($pengaduan->nama_pelapor ? $pengaduan->nama_pelapor . ': ' : '') . Str::limit($pengaduan->isi_laporan, 50),
                'tanggal' => Carbon::parse($pengaduan->created_at),
                'tanggal_formatted' => Carbon::parse($pengaduan->created_at)->format('d M Y H:i'),
                'type' => 'pengaduan',
                'badge' => 'bg-indigo-50 text-indigo-600',
                'detail' => [
                    'Judul' => $pengaduan->judul_laporan,
                    'Pelapor' => $pengaduan->nama_pelapor ?? 'Anonim',
                    'Rumah' => $pengaduan->nomor_rumah,
                    'Status' => ucfirst($pengaduan->status),
                    'Isi Laporan' => $pengaduan->isi_laporan,
                ],
            ]);
        }

        foreach ($suratTerbaru as $surat) {
            $kontenTerbaru->push([
                'id' => 'surat_' . $surat->id,
                'judul' => $surat->jenis_surat,
                'kategori' => 'Arsip Digital',
                'subkategori' => $surat->nomor_rumah,
                'keterangan' => 'Pengaju: ' . $surat->nama_pengaju . ' (' . Str::limit($surat->keperluan, 40) . ')',
                'tanggal' => Carbon::parse($surat->created_at),
                'tanggal_formatted' => Carbon::parse($surat->created_at)->format('d M Y H:i'),
                'type' => 'surat',
                'badge' => 'bg-amber-50 text-amber-700',
                'detail' => [
                    'Jenis Surat' => $surat->jenis_surat,
                    'Nama Pengaju' => $surat->nama_pengaju,
                    'Nomor Rumah' => $surat->nomor_rumah,
                    'Keperluan' => $surat->keperluan,
                    'Status' => ucfirst($surat->status),
                ],
            ]);
        }

        foreach ($wargaTerbaru as $w) {
            $kontenTerbaru->push([
                'id' => 'warga_' . $w->id,
                'judul' => $w->nama_lengkap,
                'kategori' => 'Anggota',
                'subkategori' => 'Warga ' . ucfirst($w->status_tinggal),
                'keterangan' => 'Blok/No: ' . $w->nomor_rumah . ' • NIK: ' . $w->nik,
                'tanggal' => Carbon::parse($w->created_at),
                'tanggal_formatted' => Carbon::parse($w->created_at)->format('d M Y H:i'),
                'type' => 'warga',
                'badge' => 'bg-cyan-50 text-cyan-600',
                'detail' => [
                    'Nama Lengkap' => $w->nama_lengkap,
                    'NIK' => $w->nik,
                    'Nomor Rumah' => $w->nomor_rumah,
                    'No HP' => $w->no_hp ?? '-',
                    'Status Tinggal' => ucfirst($w->status_tinggal),
                ],
            ]);
        }

        // Urutkan dari tanggal paling baru
        $kontenTerbaru = $kontenTerbaru->sortByDesc('tanggal')->values();

        return view('admin.dashboard', compact(
            'user',
            'totalMasuk',
            'totalKeluar',
            'saldoKas',
            'totalWarga',
            'totalPengaduan',
            'totalSurat',
            'totalArsip',
            'totalTransaksi',
            'totalKK',
            'totalWargaAktif',
            'pembayaranBulanIni',
            'suratMenunggu',
            'pengaduanBaru',
            'kegiatanMendatang',
            'wargaListAll',
            'kontenTerbaru',
            'transaksiTerbaru',
            'iuranWarga',
            'pengaduanTerbaru',
            'suratTerbaru'
        ));
    }

    /**
     * Simpan Transaksi Kas RT Baru dari Dashboard
     */
    public function storeKas(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'jenis' => 'required|in:masuk,keluar',
            'jumlah' => 'required|numeric|min:1',
            'kategori' => 'required|string|max:100',
            'keterangan' => 'required|string',
        ]);

        KasRt::create($validated);

        return redirect()->route('admin.dashboard')->with('success', 'Transaksi Kas RT berhasil dicatat.');
    }

    /**
     * Simpan Data Warga Baru dari Dashboard (Sinkron dengan Form Registrasi Warga)
     */
    public function storeWarga(Request $request)
    {
        return app(WargaController::class)->store($request);
    }

    /**
     * Simpan Data Iuran Warga dari Dashboard
     */
    public function storeIuran(Request $request)
    {
        $validated = $request->validate([
            'warga_id' => 'required|exists:wargas,id',
            'jenis_iuran' => 'required|in:bulanan,sampah,ronda',
            'bulan_tahun' => 'required|string',
            'jumlah' => 'required|numeric|min:1',
            'status' => 'required|in:belum_bayar,lunas',
            'tanggal_bayar' => 'nullable|date',
            'keterangan' => 'nullable|string',
        ]);

        IuranWarga::create($validated);

        return redirect()->route('admin.dashboard')->with('success', 'Catatan iuran warga berhasil ditambahkan.');
    }

    /**
     * Dashboard Warga
     */
    public function warga()
    {
        // Jalankan evaluasi otomatis 1x24 jam
        Pengaduan::autoResolveExpiredRatings();

        $user = Auth::user();

        $totalMasuk = KasRt::where('jenis', 'masuk')->sum('jumlah');
        $totalKeluar = KasRt::where('jenis', 'keluar')->sum('jumlah');
        $saldoKas = $totalMasuk - $totalKeluar;

        $wargaProfile = Warga::where('nama_lengkap', $user->name)->first();
        $nomorRumah = $wargaProfile?->nomor_rumah;

        // Ambil semua aduan & pengajuan khusus milik akun warga yang sedang login
        $semuaAduanWarga = Pengaduan::where(function ($q) use ($user, $nomorRumah) {
            // Prioritas utama: berdasarkan user_id pemilik akun
            $q->where('user_id', $user->id);

            // Jika aduan lama belum mencatat user_id, cocokkan dengan nomor rumah atau nama pelapor
            $q->orWhere(function ($sub) use ($user, $nomorRumah) {
                $sub->whereNull('user_id')
                    ->where(function ($match) use ($user, $nomorRumah) {
                        if ($nomorRumah) {
                            $match->where('nomor_rumah', $nomorRumah);
                        }
                        $match->orWhere('nama_pelapor', $user->name)
                              ->orWhere('nama_pelapor', $user->username)
                              ->orWhere('nama_pelapor', 'like', '%' . $user->name . '%')
                              ->orWhere('nama_pelapor', 'like', '%' . $user->username . '%');
                    });
            });
        })->latest()->get();

        // Pisahkan menjadi: 1. Status Pengajuan Surat Administrasi, dan 2. Pengaduan Lingkungan Warga
        $suratWarga = $semuaAduanWarga->filter(function ($item) {
            return str_contains($item->kategori ?? '', 'Surat') || 
                   str_contains($item->judul_laporan ?? '', 'Surat') ||
                   str_contains($item->judul_laporan ?? '', 'KTP') ||
                   str_contains($item->judul_laporan ?? '', 'Domisili') ||
                   str_contains($item->judul_laporan ?? '', 'SKU') ||
                   str_contains($item->judul_laporan ?? '', 'Nikah');
        });

        $pengaduanWarga = $semuaAduanWarga->reject(function ($item) {
            return str_contains($item->kategori ?? '', 'Surat') || 
                   str_contains($item->judul_laporan ?? '', 'Surat') ||
                   str_contains($item->judul_laporan ?? '', 'KTP') ||
                   str_contains($item->judul_laporan ?? '', 'Domisili') ||
                   str_contains($item->judul_laporan ?? '', 'SKU') ||
                   str_contains($item->judul_laporan ?? '', 'Nikah');
        });

        $iuranTerbaru = IuranWarga::with('warga')->latest()->take(5)->get();
        $transaksiTerbaru = KasRt::latest('tanggal')->take(5)->get();

        return view('warga.dashboard', compact(
            'user',
            'wargaProfile',
            'totalMasuk',
            'totalKeluar',
            'saldoKas',
            'iuranTerbaru',
            'transaksiTerbaru',
            'pengaduanWarga',
            'suratWarga'
        ));
    }

    /**
     * Halaman Cetak & Unduh Surat Pengantar Resmi RT (PDF)
     */
    public function downloadSurat($id)
    {
        $user = Auth::user();
        $pengaduan = Pengaduan::findOrFail($id);

        // Keamanan akses: Hanya pemilik surat atau admin yang dapat mengunduh
        if (!$user->isAdmin()) {
            $isOwner = ($pengaduan->user_id === $user->id) ||
                       ($pengaduan->nama_pelapor === $user->name) ||
                       ($pengaduan->nama_pelapor === $user->username) ||
                       str_contains(strtolower($user->name), strtolower($pengaduan->nama_pelapor ?? '')) ||
                       str_contains(strtolower($user->username), strtolower($pengaduan->nama_pelapor ?? ''));

            if (!$isOwner) {
                return redirect()->route('warga.dashboard')
                    ->with('error', 'Akses ditolak: Anda tidak memiliki izin untuk mengunduh surat ini.');
            }
        }

        // Cek apakah surat sudah berstatus selesai
        if (strtolower($pengaduan->status) !== 'selesai') {
            return redirect()->route('warga.dashboard')
                ->with('error', 'Surat belum dapat diunduh karena masih dalam proses verifikasi atau pengerjaan pengurus RT.');
        }

        // Format nomor surat resmi RT (Contoh: 470 / 008 / RT.01 / X / 2026)
        $romawiBulan = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'];
        $bulanSekarang = $romawiBulan[(int)date('n')];
        $nomorSurat = sprintf("470 / %03d / RT.01 / %s / %s", $pengaduan->id, $bulanSekarang, date('Y'));

        $wargaProfile = $pengaduan->warga ?: ($pengaduan->warga_id ? null : Warga::where('nama_lengkap', $pengaduan->nama_pelapor)->first());
        if ($pengaduan->warga_id === null && !$wargaProfile) {
            $pengaduan->nama_pelapor = 'Warga Telah Dihapus';
        }

        return view('warga.surat.cetak', compact(
            'pengaduan',
            'nomorSurat',
            'wargaProfile',
            'user'
        ));
    }

    /**
     * Endpoint API JSON untuk data realtime statistik Dashboard Admin & Notifikasi Realtime
     */
    public function realtimeStats(Request $request)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return response()->json(['error' => 'Akses ditolak.'], 403);
        }

        // Jalankan evaluasi rating 1x24 jam
        Pengaduan::autoResolveExpiredRatings();

        $suratMenunggu = Administrasi::whereIn('status', ['menunggu', 'pending'])->count();
        $pengaduanBaru = Pengaduan::whereIn('status', ['pending', 'diterima'])->where('kategori', '!=', 'Administrasi Surat')->count();
        
        $totalMasuk = KasRt::where('jenis', 'masuk')->sum('jumlah');
        $totalKeluar = KasRt::where('jenis', 'keluar')->sum('jumlah');
        $saldoKas = $totalMasuk - $totalKeluar;
        $totalWarga = Warga::count();
        $pembayaranBulanIni = IuranWarga::where('status', 'lunas')->sum('jumlah');

        $latestSurat = Administrasi::latest('id')->first();
        $latestPengaduan = Pengaduan::where('kategori', '!=', 'Administrasi Surat')->latest('id')->first();

        return response()->json([
            'success' => true,
            'surat_menunggu' => $suratMenunggu,
            'pengaduan_baru' => $pengaduanBaru,
            'saldo_kas' => $saldoKas,
            'saldo_kas_formatted' => 'Rp ' . number_format($saldoKas, 0, ',', '.'),
            'total_warga' => $totalWarga,
            'pembayaran_bulan_ini' => $pembayaranBulanIni,
            'pembayaran_bulan_ini_formatted' => 'Rp ' . number_format($pembayaranBulanIni, 0, ',', '.'),
            'latest_surat' => $latestSurat ? [
                'id' => $latestSurat->id,
                'jenis_surat' => $latestSurat->jenis_surat,
                'nama_pengaju' => $latestSurat->nama_pengaju,
                'status' => $latestSurat->status,
                'created_at' => $latestSurat->created_at?->diffForHumans(),
            ] : null,
            'latest_pengaduan' => $latestPengaduan ? [
                'id' => $latestPengaduan->id,
                'judul' => $latestPengaduan->judul_laporan,
                'nama_pelapor' => $latestPengaduan->nama_pelapor,
                'status' => $latestPengaduan->status,
                'created_at' => $latestPengaduan->created_at?->diffForHumans(),
            ] : null,
        ]);
    }
}
