<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KartuKeluarga extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'warga_id',
        'nomor_kk',
        'kepala_keluarga',
        'nik_kepala_keluarga',
        'nomor_rumah',
        'rt',
        'rw',
        'no_hp',
        'jumlah_anggota',
        'foto_kk',
        'foto_ktp',
        'foto_bpjs',
        'foto_lainnya',
        'status_verifikasi',
        'catatan_admin',
    ];

    protected $casts = [
        'jumlah_anggota' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function warga(): BelongsTo
    {
        return $this->belongsTo(Warga::class);
    }

    public function isLengkap(): bool
    {
        return !empty($this->foto_kk) && !empty($this->foto_ktp) && !empty($this->foto_bpjs);
    }

    public function getKelengkapanAttribute(): int
    {
        $count = 0;
        if (!empty($this->foto_kk)) $count++;
        if (!empty($this->foto_ktp)) $count++;
        if (!empty($this->foto_bpjs)) $count++;
        if (!empty($this->foto_lainnya)) $count++;

        return (int) round(($count / 3) * 100);
    }

    public function getFotoKkUrlAttribute(): ?string
    {
        return $this->foto_kk ? asset('storage/' . $this->foto_kk) : null;
    }

    public function getFotoKtpUrlAttribute(): ?string
    {
        return $this->foto_ktp ? asset('storage/' . $this->foto_ktp) : null;
    }

    public function getFotoBpjsUrlAttribute(): ?string
    {
        return $this->foto_bpjs ? asset('storage/' . $this->foto_bpjs) : null;
    }

    public function getFotoLainnyaUrlAttribute(): ?string
    {
        return $this->foto_lainnya ? asset('storage/' . $this->foto_lainnya) : null;
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status_verifikasi) {
            'terverifikasi' => 'Terverifikasi',
            'ditolak' => 'Ditolak',
            default => 'Menunggu Verifikasi',
        };
    }
}
