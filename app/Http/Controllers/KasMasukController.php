<?php

namespace App\Http\Controllers;

use App\Models\KasRt;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class KasMasukController extends Controller
{
    /**
     * Menampilkan daftar transaksi Kas Masuk Admin
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$user->isAdmin()) {
            return redirect()->route('warga.dashboard')->with('error', 'Akses ditolak: Khusus Pengurus RT.');
        }

        // Ambil data transaksi kas masuk, diurutkan tanggal terbaru
        $kasMasukQuery = KasRt::where('jenis', 'masuk')
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc');

        $kasMasukList = $kasMasukQuery->get();

        // Hitung ringkasan statistik
        $totalKasMasuk = $kasMasukList->sum('jumlah');
        $kasBulanIni = KasRt::where('jenis', 'masuk')
            ->whereMonth('tanggal', Carbon::now()->month)
            ->whereYear('tanggal', Carbon::now()->year)
            ->sum('jumlah');
        $totalTransaksi = $kasMasukList->count();

        // Format data untuk Alpine.js interaktif
        $kasMasukFormatted = $kasMasukList->map(function ($item) {
            return [
                'id' => $item->id,
                'tanggal' => Carbon::parse($item->tanggal)->format('Y-m-d'),
                'sumber_dana' => $item->kategori,
                'keterangan' => $item->keterangan,
                'nominal' => (float) $item->jumlah,
                'nominal_formatted' => '+ Rp ' . number_format($item->jumlah, 0, ',', '.'),
                'lampiran' => $item->lampiran,
                'lampiran_url' => $item->lampiran ? asset('storage/' . $item->lampiran) : null,
                'is_pdf' => $item->lampiran ? str_ends_with(strtolower($item->lampiran), '.pdf') : false,
            ];
        });

        // Opsi default sumber dana
        $sumberDanaOptions = [
            'Donasi',
            'Iuran',
            'Bantuan Pemerintah',
            'Sponsorship',
            'Lainnya',
        ];

        return view('admin.kas-masuk.index', compact(
            'user',
            'kasMasukFormatted',
            'totalKasMasuk',
            'kasBulanIni',
            'totalTransaksi',
            'sumberDanaOptions'
        ));
    }

    /**
     * Menyimpan transaksi Kas Masuk baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'sumber_dana' => 'required|string|max:100',
            'sumber_dana_custom' => 'nullable|string|max:100',
            'keterangan' => 'required|string|max:500',
            'jumlah' => 'required|numeric|min:1',
            'lampiran' => 'nullable|file|mimes:jpeg,png,jpg,pdf,webp|max:5120',
        ]);

        $sumberDana = $validated['sumber_dana'];
        if ($sumberDana === 'Lainnya' && !empty($validated['sumber_dana_custom'])) {
            $sumberDana = $validated['sumber_dana_custom'];
        }

        $lampiranPath = null;
        if ($request->hasFile('lampiran')) {
            $lampiranPath = $request->file('lampiran')->store('lampiran_kas', 'public');
        }

        KasRt::create([
            'tanggal' => $validated['tanggal'],
            'jenis' => 'masuk',
            'kategori' => $sumberDana,
            'keterangan' => $validated['keterangan'],
            'jumlah' => $validated['jumlah'],
            'lampiran' => $lampiranPath,
        ]);

        return redirect()->route('admin.kas-masuk.index')->with('success', 'Transaksi Kas Masuk berhasil ditambahkan.');
    }

    /**
     * Memperbarui transaksi Kas Masuk
     */
    public function update(Request $request, $id)
    {
        $kas = KasRt::where('jenis', 'masuk')->findOrFail($id);

        $validated = $request->validate([
            'tanggal' => 'required|date',
            'sumber_dana' => 'required|string|max:100',
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
            'kategori' => $validated['sumber_dana'],
            'keterangan' => $validated['keterangan'],
            'jumlah' => $validated['jumlah'],
            'lampiran' => $lampiranPath,
        ]);

        return redirect()->route('admin.kas-masuk.index')->with('success', 'Data transaksi Kas Masuk berhasil diperbarui.');
    }

    /**
     * Menghapus transaksi Kas Masuk
     */
    public function destroy($id)
    {
        $kas = KasRt::where('jenis', 'masuk')->findOrFail($id);

        if ($kas->lampiran && Storage::disk('public')->exists($kas->lampiran)) {
            Storage::disk('public')->delete($kas->lampiran);
        }

        $kas->delete();

        return redirect()->route('admin.kas-masuk.index')->with('success', 'Transaksi Kas Masuk berhasil dihapus.');
    }
}
