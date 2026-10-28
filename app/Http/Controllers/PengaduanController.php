<?php

namespace App\Http\Controllers;

use App\Models\Pengaduan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PengaduanController extends Controller
{
    /**
     * Menampilkan daftar pengaduan warga untuk Admin.
     */
    public function index(Request $request)
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            return redirect()->route('warga.dashboard')->with('error', 'Akses ditolak: Halaman tersebut khusus untuk Pengurus RT.');
        }

        // Jalankan otomatisasi rating 1x24 jam secara real-time
        Pengaduan::autoResolveExpiredRatings();

        // Ambil data pengaduan terbaru
        $pengaduans = Pengaduan::orderBy('created_at', 'desc')->get();

        // Ringkasan data untuk header/kartu metrik (opsional & memperkaya informasi)
        $totalAduan = $pengaduans->count();
        $totalDiterima = $pengaduans->filter(fn($p) => in_array(strtolower($p->status), ['pending', 'diterima']))->count();
        $totalDiproses = $pengaduans->where('status', 'diproses')->count();
        $totalSelesai = $pengaduans->where('status', 'selesai')->count();
        $rataRataRating = $pengaduans->whereNotNull('rating')->avg('rating') ?: 0;

        if ($request->wantsJson() || $request->ajax()) {
            $formattedPengaduans = $pengaduans->map(function ($item) {
                return [
                    'id' => $item->id,
                    'tanggal' => $item->created_at ? $item->created_at->format('Y-m-d') : '-',
                    'tanggal_lengkap' => $item->created_at ? $item->created_at->translatedFormat('d F Y, H:i') . ' WIB' : '-',
                    'nama_warga' => $item->warga ? $item->warga->nama_lengkap : ($item->warga_id === null ? 'Warga Telah Dihapus' : ($item->nama_pelapor ?: 'Anonim')),
                    'nomor_whatsapp' => $item->nomor_whatsapp ?: '-',
                    'nomor_rumah' => $item->nomor_rumah ?: '-',
                    'kategori' => $item->kategori ?: 'Layanan Umum',
                    'judul' => $item->judul_laporan,
                    'isi' => $item->isi_laporan,
                    'foto_url' => $item->foto ? asset('storage/' . $item->foto) : null,
                    'status' => strtolower($item->status),
                    'status_label' => $item->status_label,
                    'rating' => $item->rating,
                    'ulasan_warga' => $item->ulasan_warga,
                    'selesai_at' => $item->selesai_at ? $item->selesai_at->toIso8601String() : null,
                    'selesai_at_lengkap' => $item->selesai_at ? $item->selesai_at->translatedFormat('d F Y, H:i') . ' WIB' : '-',
                    'is_menunggu_rating' => $item->isMenungguRatingWarga(),
                ];
            });

            return response()->json([
                'success' => true,
                'pengaduans' => $formattedPengaduans,
                'totalAduan' => $totalAduan,
                'totalDiterima' => $totalDiterima,
                'totalDiproses' => $totalDiproses,
                'totalSelesai' => $totalSelesai,
                'rataRataRating' => round($rataRataRating, 1),
            ]);
        }

        return view('admin.pengaduan.index', compact(
            'pengaduans',
            'totalAduan',
            'totalDiterima',
            'totalDiproses',
            'totalSelesai',
            'rataRataRating'
        ));
    }

    /**
     * Memperbarui status aduan (misal: 'diproses' atau 'selesai').
     * Saat admin mengubah ke 'selesai', isi otomatis selesai_at = now().
     */
    public function updateStatus(Request $request, $id)
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak: Anda saat ini login sebagai akun Warga. Silakan login kembali dengan akun Pengurus RT (Admin).'
                ], 403);
            }
            return redirect()->route('login')->with('error', 'Akses ditolak: Anda harus login sebagai Pengurus RT (Admin).');
        }

        $request->validate([
            'status' => 'required|in:diterima,pending,diproses,selesai',
        ]);

        $pengaduan = Pengaduan::findOrFail($id);
        $newStatus = strtolower($request->status);

        if ($newStatus === 'selesai') {
            $pengaduan->status = 'selesai';
            // Isi otomatis timestamp pengerjaan jika belum ada
            if (!$pengaduan->selesai_at) {
                $pengaduan->selesai_at = Carbon::now();
            }
        } elseif ($newStatus === 'diproses') {
            $pengaduan->status = 'diproses';
            // Jika dikembalikan dari selesai, reset waktu selesai dan rating
            $pengaduan->selesai_at = null;
            $pengaduan->rating = null;
            $pengaduan->ulasan_warga = null;
        } else {
            $pengaduan->status = 'diterima';
            $pengaduan->selesai_at = null;
            $pengaduan->rating = null;
            $pengaduan->ulasan_warga = null;
        }

        $pengaduan->save();

        $message = "Status pengaduan \"{$pengaduan->judul_laporan}\" berhasil diperbarui menjadi {$pengaduan->status_label}.";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'item' => [
                    'id' => $pengaduan->id,
                    'status' => strtolower($pengaduan->status),
                    'status_label' => $pengaduan->status_label,
                    'selesai_at' => $pengaduan->selesai_at ? $pengaduan->selesai_at->format('Y-m-d H:i') : null,
                    'selesai_at_lengkap' => $pengaduan->selesai_at ? $pengaduan->selesai_at->translatedFormat('d F Y, H:i') . ' WIB' : '-',
                    'is_menunggu_rating' => $pengaduan->isMenungguRatingWarga(),
                ]
            ]);
        }

        return redirect()->route('admin.pengaduan.index')
            ->with('success', $message);
    }

    /**
     * Menyimpan pengaduan baru (jika diinput melalui antarmuka admin).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_rumah' => 'required|string|max:50',
            'nama_pelapor' => 'nullable|string|max:100',
            'kategori' => 'required|string|max:100',
            'judul_laporan' => 'required|string|max:200',
            'isi_laporan' => 'required|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        $data = [
            'nomor_rumah' => $validated['nomor_rumah'],
            'nama_pelapor' => $validated['nama_pelapor'] ?? 'Anonim',
            'kategori' => $validated['kategori'],
            'judul_laporan' => $validated['judul_laporan'],
            'isi_laporan' => $validated['isi_laporan'],
            'status' => 'diterima',
        ];

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('pengaduan', 'public');
            $data['foto'] = $path;
        }

        Pengaduan::create($data);

        return redirect()->route('admin.pengaduan.index')
            ->with('success', 'Pengaduan warga berhasil ditambahkan.');
    }

    /**
     * Memperbarui rating dan ulasan oleh warga atau evaluasi manual.
     */
    public function updateRating(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'ulasan_warga' => 'nullable|string|max:500',
        ]);

        $pengaduan = Pengaduan::findOrFail($id);
        $pengaduan->rating = (int) $request->rating;
        $pengaduan->ulasan_warga = $request->ulasan_warga ?? 'Layanan diselesaikan dengan baik.';
        $pengaduan->save();

        return redirect()->back()->with('success', 'Rating dan ulasan evaluasi pelayanan berhasil disimpan.');
    }

    /**
     * Menghapus aduan.
     */
    public function destroy($id)
    {
        $pengaduan = Pengaduan::findOrFail($id);

        if ($pengaduan->foto && Storage::disk('public')->exists($pengaduan->foto)) {
            Storage::disk('public')->delete($pengaduan->foto);
        }

        $judul = $pengaduan->judul_laporan;
        $pengaduan->delete();

        return redirect()->route('admin.pengaduan.index')
            ->with('success', "Pengaduan \"{$judul}\" berhasil dihapus.");
    }
}
