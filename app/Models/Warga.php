<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warga extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama_lengkap',
        'tempat_lahir',
        'tanggal_lahir',
        'nik',
        'no_kk',
        'status_keluarga',
        'nomor_rumah',
        'blok',
        'no_hp',
        'status_tinggal',
        'is_berkeluarga',
        'memiliki_anak',
        'file_ktp',
        'file_kk',
        'is_profile_complete',
        'kartu_keluarga_id',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'is_berkeluarga' => 'boolean',
        'memiliki_anak' => 'boolean',
        'is_profile_complete' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function anaks(): HasMany
    {
        return $this->hasMany(WargaAnak::class, 'warga_id');
    }

    public function kartuKeluarga(): BelongsTo
    {
        return $this->belongsTo(KartuKeluarga::class, 'kartu_keluarga_id');
    }

    /**
     * Cari berkas KK yang cocok (berdasarkan kartu_keluarga_id, no_kk, atau nama/rumah)
     */
    public function getBerkasKkAttribute(): ?KartuKeluarga
    {
        if ($this->kartu_keluarga_id && $this->relationLoaded('kartuKeluarga') && $this->kartuKeluarga) {
            return $this->kartuKeluarga;
        }

        if ($this->kartu_keluarga_id) {
            $kk = KartuKeluarga::find($this->kartu_keluarga_id);
            if ($kk) return $kk;
        }

        if (!empty($this->no_kk)) {
            $kk = KartuKeluarga::where('nomor_kk', $this->no_kk)->first();
            if ($kk) return $kk;
        }

        return KartuKeluarga::where('kepala_keluarga', $this->nama_lengkap)
            ->orWhere('nomor_rumah', $this->nomor_rumah)
            ->first();
    }

    /**
     * Otomatis deteksi atau kembalikan nama Blok
     */
    public function getBlokNameAttribute(): string
    {
        if (!empty($this->blok)) {
            return $this->blok;
        }

        if (preg_match('/(Blok\s+[A-Za-z0-9]+)/i', $this->nomor_rumah ?? '', $matches)) {
            return ucwords(strtolower($matches[1]));
        }

        return 'Lainnya';
    }

    public function iuranWargas(): HasMany
    {
        return $this->hasMany(IuranWarga::class);
    }

    public function pengaduans(): HasMany
    {
        return $this->hasMany(Pengaduan::class);
    }
}
