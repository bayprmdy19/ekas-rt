<?php

namespace App\Http\Controllers;

use App\Models\IuranWarga;
use App\Models\MasterIuran;
use App\Models\Warga;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IuranController extends Controller
{
    /**
     * Tampilkan Halaman Iuran & Tagihan Admin
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$user->isAdmin()) {
            return redirect()->route('warga.dashboard')->with('error', 'Akses ditolak: Khusus Pengurus RT.');
        }

        // Ambil data Master Iuran
        $masterIurans = MasterIuran::orderBy('id', 'asc')->get();

        // Ambil data Tagihan Warga
        $tagihanQuery = IuranWarga::with('warga')->latest('id');

        if ($request->filled('status_tagihan') && $request->status_tagihan !== 'semua') {
            $tagihanQuery->where('status', $request->status_tagihan);
        }

        if ($request->filled('tahun')) {
            $tagihanQuery->where('bulan_tahun', 'like', $request->tahun . '-%');
        }

        $daftarTagihan = $tagihanQuery->take(30)->get();

        // Format master iuran untuk konsumsi javascript di frontend
        $masterIuransFormatted = $masterIurans->map(function ($iuran) {
            return [
                'id' => $iuran->id,
                'nama_iuran' => $iuran->nama_iuran,
                'nominal' => (float)$iuran->nominal,
                'tanggal_mulai' => $iuran->tanggal_mulai ? $iuran->tanggal_mulai->format('Y-m-d') : '',
                'tanggal_mulai_formatted' => $iuran->tanggal_mulai ? $iuran->tanggal_mulai->format('Y-m-d') : '-',
                'status' => $iuran->status,
                'keterangan' => $iuran->keterangan ?? '',
            ];
        });

        // Daftar semua warga aktif untuk pembuatan tagihan
        $wargaAktif = Warga::orderBy('nama_lengkap', 'asc')->get();

        return view('admin.iuran.index', compact(
            'user',
            'masterIurans',
            'masterIuransFormatted',
            'daftarTagihan',
            'wargaAktif'
        ));
    }

    /**
     * Simpan Master Iuran Baru
     */
    public function storeMaster(Request $request)
    {
        $validated = $request->validate([
            'nama_iuran' => 'required|string|max:150',
            'nominal' => 'required|numeric|min:0',
            'tanggal_mulai' => 'required|date',
            'status' => 'required|in:Aktif,Nonaktif',
            'keterangan' => 'nullable|string',
        ]);

        MasterIuran::create($validated);

        return redirect()->route('admin.iuran.index')->with('success', 'Master iuran baru berhasil ditambahkan.');
    }

    /**
     * Update Master Iuran
     */
    public function updateMaster(Request $request, $id)
    {
        $masterIuran = MasterIuran::findOrFail($id);

        $validated = $request->validate([
            'nama_iuran' => 'required|string|max:150',
            'nominal' => 'required|numeric|min:0',
            'tanggal_mulai' => 'required|date',
            'status' => 'required|in:Aktif,Nonaktif',
            'keterangan' => 'nullable|string',
        ]);

        $masterIuran->update($validated);

        return redirect()->route('admin.iuran.index')->with('success', 'Data master iuran berhasil diperbarui.');
    }

    /**
     * Hapus Master Iuran
     */
    public function destroyMaster($id)
    {
        $masterIuran = MasterIuran::findOrFail($id);
        $masterIuran->delete();

        return redirect()->route('admin.iuran.index')->with('success', 'Data iuran berhasil dihapus.');
    }

    /**
     * Generate Tagihan Masal untuk Semua Warga Aktif
     */
    public function generateTagihan(Request $request)
    {
        $validated = $request->validate([
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer|between:2020,2035',
        ]);

        $bulanStr = str_pad($validated['bulan'], 2, '0', STR_PAD_LEFT);
        $bulanTahun = $validated['tahun'] . '-' . $bulanStr;

        // Ambil seluruh master iuran yang aktif
        $iuransAktif = MasterIuran::where('status', 'Aktif')->get();
        if ($iuransAktif->isEmpty()) {
            return redirect()->route('admin.iuran.index')->with('error', 'Tidak ada Master Iuran berstatus Aktif untuk di-generate.');
        }

        $wargas = Warga::all();
        if ($wargas->isEmpty()) {
            return redirect()->route('admin.iuran.index')->with('error', 'Tidak ada data warga aktif.');
        }

        $generatedCount = 0;
        foreach ($wargas as $warga) {
            foreach ($iuransAktif as $iuran) {
                // Cek apakah sudah ada tagihan untuk warga, jenis iuran, dan periode ini
                $exists = IuranWarga::where('warga_id', $warga->id)
                    ->where('jenis_iuran', $iuran->nama_iuran)
                    ->where('bulan_tahun', $bulanTahun)
                    ->exists();

                if (!$exists) {
                    IuranWarga::create([
                        'warga_id' => $warga->id,
                        'jenis_iuran' => $iuran->nama_iuran,
                        'bulan_tahun' => $bulanTahun,
                        'jumlah' => $iuran->nominal,
                        'status' => 'belum_bayar',
                        'keterangan' => $validated['bulan'] . '/' . $validated['tahun'],
                    ]);
                    $generatedCount++;
                }
            }
        }

        return redirect()->route('admin.iuran.index')->with('success', "Berhasil men-generate {$generatedCount} tagihan untuk periode {$bulanStr}/{$validated['tahun']}.");
    }

    /**
     * Toggle Status Pembayaran Tagihan (Lunas / Belum Lunas)
     */
    public function toggleStatusTagihan($id)
    {
        $tagihan = IuranWarga::findOrFail($id);

        if ($tagihan->status === 'lunas') {
            $tagihan->status = 'belum_bayar';
            $tagihan->tanggal_bayar = null;
        } else {
            $tagihan->status = 'lunas';
            $tagihan->tanggal_bayar = now();
        }

        $tagihan->save();

        return redirect()->route('admin.iuran.index')->with('success', 'Status pembayaran tagihan berhasil diubah.');
    }
}
