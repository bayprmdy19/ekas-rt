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
     * Booted hook untuk evaluasi realtime setiap kali model dimuat
     */
    protected static function booted()
    {
        static::retrieved(function ($pengaduan) {
            if ($pengaduan->status === 'selesai' && is_null($pengaduan->attributes['rating'] ?? null)) {
                $finishTime = $pengaduan->selesai_at ?? $pengaduan->updated_at;
                if ($finishTime && Carbon::now()->diffInSeconds($finishTime) >= 24 * 3600) {
                    $pengaduan->attributes['rating'] = 5;
                    $pengaduan->attributes['ulasan_warga'] = $pengaduan->attributes['ulasan_warga'] ?? 'Otomatis selesai oleh sistem (Layanan tuntas)';
                    $pengaduan->saveQuietly();
                }
            }
        });
    }

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
     * Sisa waktu dalam detik sebelum evaluasi otomatis rating 5 (1x24 jam).
     */
    public function getSisaWaktuRatingDetik(): int
    {
        if ($this->status !== 'selesai' || !is_null($this->attributes['rating'] ?? null)) {
            return 0;
        }

        $finishTime = $this->selesai_at ?? $this->updated_at;
        if (!$finishTime) {
            return 24 * 3600;
        }

        $expiry = $finishTime->copy()->addHours(24);
        $diff = Carbon::now()->diffInSeconds($expiry, false);

        return max(0, (int) $diff);
    }

    /**
     * Mengecek apakah aduan berstatus selesai namun masih dalam rentang waktu tunggu rating 1x24 jam dari warga.
     */
    public function isMenungguRatingWarga(): bool
    {
        if ($this->status !== 'selesai' || !is_null($this->attributes['rating'] ?? null)) {
            return false;
        }

        $finishTime = $this->selesai_at ?? $this->updated_at;
        if (!$finishTime) {
            return true;
        }

        return Carbon::now()->diffInSeconds($finishTime) < 24 * 3600;
    }

    /**
     * Accessor Rating: Jika selesai dan telah melewati 1x24 jam tanpa rating, otomatis kembalikan 5 secara realtime.
     */
    public function getRatingAttribute($value): ?int
    {
        if ($this->status === 'selesai' && is_null($value)) {
            $finishTime = $this->selesai_at ?? $this->updated_at;
            if ($finishTime && Carbon::now()->diffInSeconds($finishTime) >= 24 * 3600) {
                return 5;
            }
        }

        return $value !== null ? (int) $value : null;
    }

    /**
     * Accessor Ulasan Warga: Jika selesai dan otomatisasi 5 bintang berlaku, kembalikan ulasan sistem.
     */
    public function getUlasanWargaAttribute($value): ?string
    {
        if ($this->status === 'selesai' && is_null($this->attributes['rating'] ?? null) && is_null($value)) {
            $finishTime = $this->selesai_at ?? $this->updated_at;
            if ($finishTime && Carbon::now()->diffInSeconds($finishTime) >= 24 * 3600) {
                return 'Otomatis selesai oleh sistem (Layanan tuntas)';
            }
        }

        return $value;
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
