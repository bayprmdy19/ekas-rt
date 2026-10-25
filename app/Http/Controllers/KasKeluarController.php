<?php

namespace App\Http\Controllers;

use App\Models\KasRt;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class KasKeluarController extends Controller
{
    /**
     * Menampilkan daftar transaksi Kas Keluar Admin
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$user->isAdmin()) {
            return redirect()->route('warga.dashboard')->with('error', 'Akses ditolak: Khusus Pengurus RT.');
        }

        // Ambil data transaksi kas keluar, diurutkan tanggal terbaru
        $kasKeluarQuery = KasRt::where('jenis', 'keluar')
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc');

        $kasKeluarList = $kasKeluarQuery->get();

        // Hitung ringkasan statistik
        $totalKasKeluar = $kasKeluarList->sum('jumlah');
        $kasBulanIni = KasRt::where('jenis', 'keluar')
            ->whereMonth('tanggal', Carbon::now()->month)
            ->whereYear('tanggal', Carbon::now()->year)
            ->sum('jumlah');
        $totalTransaksi = $kasKeluarList->count();

        // Format data untuk Alpine.js interaktif
        $kasKeluarFormatted = $kasKeluarList->map(function ($item) {
            return [
                'id' => $item->id,
                'tanggal' => Carbon::parse($item->tanggal)->format('Y-m-d'),
                'kategori' => $item->kategori,
                'keterangan' => $item->keterangan,
                'nominal' => (float) $item->jumlah,
                'nominal_formatted' => '- Rp ' . number_format($item->jumlah, 0, ',', '.'),
                'lampiran' => $item->lampiran,
                'lampiran_url' => $item->lampiran ? asset('storage/' . $item->lampiran) : null,
                'is_pdf' => $item->lampiran ? str_ends_with(strtolower($item->lampiran), '.pdf') : false,
            ];
        });

        // Opsi kategori pengeluaran default
        $kategoriOptions = [
            'Kebersihan',
            'Sosial',
            'Keamanan',
            'Perbaikan & Fasilitas',
            'Konsumsi & Rapat',
            'Operasional RT',
            'Lainnya',
        ];

        return view('admin.kas-keluar.index', compact(
            'user',
            'kasKeluarFormatted',
            'totalKasKeluar',
            'kasBulanIni',
            'totalTransaksi',
            'kategoriOptions'
        ));
    }

    /**
     * Menyimpan transaksi Kas Keluar baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'kategori' => 'required|string|max:100',
            'kategori_custom' => 'nullable|string|max:100',
            'keterangan' => 'required|string|max:500',
            'jumlah' => 'required|numeric|min:1',
            'lampiran' => 'nullable|file|mimes:jpeg,png,jpg,pdf,webp|max:5120',
        ]);

        $kategori = $validated['kategori'];
        if ($kategori === 'Lainnya' && !empty($validated['kategori_custom'])) {
            $kategori = $validated['kategori_custom'];
        }

        $lampiranPath = null;
        if ($request->hasFile('lampiran')) {
            $lampiranPath = $request->file('lampiran')->store('lampiran_kas', 'public');
        }

        KasRt::create([
            'tanggal' => $validated['tanggal'],
            'jenis' => 'keluar',
            'kategori' => $kategori,
            'keterangan' => $validated['keterangan'],
            'jumlah' => $validated['jumlah'],
            'lampiran' => $lampiranPath,
        ]);

        return redirect()->route('admin.kas-keluar.index')->with('success', 'Transaksi Kas Keluar berhasil dicatat.');
    }

    /**
     * Memperbarui transaksi Kas Keluar
     */
    public function update(Request $request, $id)
    {
        $kas = KasRt::where('jenis', 'keluar')->findOrFail($id);

        $validated = $request->validate([
            'tanggal' => 'required|date',
            'kategori' => 'required|string|max:100',
            'keterangan' => 'required|string|max:500',
            'jumlah' => 'required|numeric|min:1',
            'lampiran' => 'nullable|file|mimes:jpeg,png,jpg,pdf,webp|max:5120',
        ]);

        $lampiranPath = $kas->lampiran;
        if ($request->hasFile('lampiran')) {
            // Hapus lampiran lama jika ada
            if ($kas->lampiran && Storage::disk('public')->exists($kas->lampiran)) {
                Storage::disk('public')->delete($kas->lampiran);
            }
            $lampiranPath = $request->file('lampiran')->store('lampiran_kas', 'public');
        }

        $kas->update([
            'tanggal' => $validated['tanggal'],
            'kategori' => $validated['kategori'],
            'keterangan' => $validated['keterangan'],
            'jumlah' => $validated['jumlah'],
            'lampiran' => $lampiranPath,
        ]);

        return redirect()->route('admin.kas-keluar.index')->with('success', 'Data transaksi Kas Keluar berhasil diperbarui.');
    }

    /**
     * Menghapus transaksi Kas Keluar
     */
    public function destroy($id)
    {
        $kas = KasRt::where('jenis', 'keluar')->findOrFail($id);

        if ($kas->lampiran && Storage::disk('public')->exists($kas->lampiran)) {
            Storage::disk('public')->delete($kas->lampiran);
        }

        $kas->delete();

        return redirect()->route('admin.kas-keluar.index')->with('success', 'Transaksi Kas Keluar berhasil dihapus.');
    }
}
