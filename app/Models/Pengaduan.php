<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengaduan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nomor_rumah',
        'nama_pelapor',
        'kategori',
        'judul_laporan',
        'isi_laporan',
        'foto',
        'status',
        'rating',
        'ulasan_warga',
        'selesai_at',
    ];

    protected $casts = [
        'rating' => 'integer',
        'selesai_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Otomatisasi evaluasi pelayanan:
     * Jika aduan berstatus 'selesai' dan warga belum memberi rating dalam waktu 1x24 jam sejak selesai_at,
     * sistem otomatis menetapkan rating = 5 dan ulasan_warga = 'Otomatis selesai oleh sistem (Layanan tuntas)'.
     */
    public static function autoResolveExpiredRatings(): int
    {
        $cutoff = Carbon::now()->subHours(24);

        $affected = static::where('status', 'selesai')
            ->whereNull('rating')
            ->where(function ($query) use ($cutoff) {
                $query->where('selesai_at', '<=', $cutoff)
                    ->orWhere(function ($sub) use ($cutoff) {
                        $sub->whereNull('selesai_at')
                            ->where('updated_at', '<=', $cutoff);
                    });
            })
            ->update([
                'rating' => 5,
                'ulasan_warga' => 'Otomatis selesai oleh sistem (Layanan tuntas)',
            ]);

        return $affected;
    }

    /**
     * Mengecek apakah aduan berstatus selesai namun masih dalam rentang waktu tunggu rating 1x24 jam dari warga.
     */
    public function isMenungguRatingWarga(): bool
    {
        if ($this->status !== 'selesai' || !is_null($this->rating)) {
            return false;
        }

        $finishTime = $this->selesai_at ?? $this->updated_at;
        if (!$finishTime) {
            return true;
        }

        return Carbon::now()->diffInHours($finishTime) < 24;
    }

    /**
     * Normalisasi status tampilan (pending / diterima / diproses / selesai).
     */
    public function getStatusLabelAttribute(): string
    {
        return match (strtolower($this->status)) {
            'pending', 'diterima' => 'Diterima',
            'diproses' => 'Diproses',
            'selesai' => 'Selesai',
            default => ucfirst($this->status),
        };
    }

    public function warga(): BelongsTo
    {
        return $this->belongsTo(Warga::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
