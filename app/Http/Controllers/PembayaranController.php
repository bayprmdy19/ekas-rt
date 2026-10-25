<?php

namespace App\Http\Controllers;

use App\Models\IuranWarga;
use App\Models\KasRt;
use App\Models\MasterIuran;
use App\Models\RekeningRt;
use App\Models\Warga;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PembayaranController extends Controller
{
    /**
     * Tampilkan Halaman Manajemen Pembayaran Admin
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$user->isAdmin()) {
            return redirect()->route('warga.dashboard')->with('error', 'Akses ditolak: Khusus Pengurus RT.');
        }

        // Ambil riwayat pembayaran (yang sudah memiliki status atau transaksi)
        $pembayaranQuery = IuranWarga::with('warga')->latest('tanggal_bayar')->latest('id');

        if ($request->filled('status') && $request->status !== 'semua') {
            $pembayaranQuery->where('status', $request->status);
        }

        $riwayatPembayaran = $pembayaranQuery->get();

        // Format data untuk Alpine.js table reactivity
        $riwayatPembayaranFormatted = $riwayatPembayaran->map(function ($item) {
            $tgl = $item->tanggal_bayar ? Carbon::parse($item->tanggal_bayar)->format('Y-m-d') : ($item->created_at ? $item->created_at->format('Y-m-d') : '-');
            return [
                'id' => $item->id,
                'tanggal' => $tgl,
                'nama_warga' => $item->warga->nama_lengkap ?? 'Warga RT',
                'nomor_rumah' => $item->warga->nomor_rumah ?? '-',
                'bln_thn' => $item->keterangan ?? $item->bulan_tahun,
                'jenis_iuran' => $item->jenis_iuran,
                'nominal' => (float)$item->jumlah,
                'metode' => $item->metode_pembayaran ?? 'Transfer Bank',
                'status' => $item->status === 'lunas' ? 'Lunas' : 'Belum Lunas',
                'bukti_bayar' => $item->bukti_bayar,
            ];
        });

        // Ambil data rekening RT & E-Wallet
        $rekenings = RekeningRt::where('is_active', true)->get();

        // Ambil jenis iuran aktif
        $masterIurans = MasterIuran::where('status', 'Aktif')->get();

        // Ambil daftar warga untuk dropdown pencarian
        $wargas = Warga::orderBy('nama_lengkap', 'asc')->get();

        return view('admin.pembayaran.index', compact(
            'user',
            'riwayatPembayaran',
            'riwayatPembayaranFormatted',
            'rekenings',
            'masterIurans',
            'wargas'
        ));
    }

    /**
     * Catat Input Pembayaran Warga dari Form
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'warga_id' => 'required|exists:wargas,id',
            'jenis_iuran' => 'required|string',
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer|between:2020,2035',
            'jumlah' => 'required|numeric|min:1',
            'metode_pembayaran' => 'nullable|string',
            'tanggal_bayar' => 'nullable|date',
            'bukti_bayar' => 'nullable|image|max:2048',
        ]);

        $bulanStr = str_pad($validated['bulan'], 2, '0', STR_PAD_LEFT);
        $bulanTahun = $validated['tahun'] . '-' . $bulanStr;

        $buktiPath = null;
        if ($request->hasFile('bukti_bayar')) {
            $buktiPath = $request->file('bukti_bayar')->store('bukti_bayar', 'public');
        }

        $tanggalBayar = $validated['tanggal_bayar'] ?? now()->toDateString();
        $metode = $validated['metode_pembayaran'] ?? 'Transfer Bank';

        // Buat record pembayaran
        $iuran = IuranWarga::create([
            'warga_id' => $validated['warga_id'],
            'jenis_iuran' => $validated['jenis_iuran'],
            'bulan_tahun' => $bulanTahun,
            'jumlah' => $validated['jumlah'],
            'status' => 'lunas',
            'metode_pembayaran' => $metode,
            'bukti_bayar' => $buktiPath,
            'tanggal_bayar' => $tanggalBayar,
            'keterangan' => $validated['bulan'] . '/' . $validated['tahun'],
        ]);

        // Otomatis catat sebagai Kas RT Masuk
        $warga = Warga::find($validated['warga_id']);
        KasRt::create([
            'tanggal' => $tanggalBayar,
            'kategori' => 'Iuran ' . $validated['jenis_iuran'],
            'jenis' => 'masuk',
            'jumlah' => $validated['jumlah'],
            'keterangan' => "Pembayaran {$validated['jenis_iuran']} {$validated['bulan']}/{$validated['tahun']} a.n " . ($warga->nama_lengkap ?? 'Warga'),
        ]);

        return redirect()->route('admin.pembayaran.index')->with('success', 'Pembayaran warga berhasil dicatat dan masuk ke kas RT.');
    }

    /**
     * Tambah Info Rekening Baru
     */
    public function storeRekening(Request $request)
    {
        $validated = $request->validate([
            'nama_bank' => 'required|string|max:50',
            'nomor_rekening' => 'required|string|max:50',
            'atas_nama' => 'required|string|max:100',
            'jenis' => 'required|in:bank,ewallet',
        ]);

        RekeningRt::create($validated);

        return redirect()->route('admin.pembayaran.index')->with('success', 'Rekening baru berhasil ditambahkan.');
    }

    /**
     * Hapus Info Rekening
     */
    public function destroyRekening($id)
    {
        $rekening = RekeningRt::findOrFail($id);
        $rekening->delete();

        return redirect()->route('admin.pembayaran.index')->with('success', 'Rekening berhasil dihapus.');
    }
}
