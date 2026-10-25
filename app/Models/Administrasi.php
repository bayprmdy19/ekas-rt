<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Administrasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nomor_surat',
        'nomor_rumah',
        'nama_pengaju',
        'jenis_surat',
        'keperluan',
        'status',
        'catatan',
    ];

    /**
     * Relasi ke model User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Label status standar (Disetujui, Menunggu, Ditolak)
     */
    public function getStatusLabelAttribute(): string
    {
        return match (strtolower($this->status)) {
            'disetujui', 'selesai' => 'Disetujui',
            'ditolak' => 'Ditolak',
            'menunggu', 'pending' => 'Menunggu',
            default => ucfirst($this->status),
        };
    }

    /**
     * Generate otomatis nomor surat jika belum diatur
     */
    public function getNomorSuratDisplayAttribute(): string
    {
        if (!empty($this->nomor_surat)) {
            return $this->nomor_surat;
        }

        // Tentukan kode surat berdasarkan jenis surat
        $kode = 'PRT';
        $jenis = strtolower($this->jenis_surat);
        if (str_contains($jenis, 'domisili')) $kode = 'DOM';
        elseif (str_contains($jenis, 'usaha') || str_contains($jenis, 'sku')) $kode = 'SKU';
        elseif (str_contains($jenis, 'nikah')) $kode = 'NIK';
        elseif (str_contains($jenis, 'ktp') || str_contains($jenis, 'keluarga')) $kode = 'KTP';

        $bulan = $this->created_at ? $this->created_at->format('m') : date('m');
        $tahun = $this->created_at ? $this->created_at->format('Y') : date('Y');

        return sprintf("%03d/%s/RT/%s/%s", $this->id, $kode, $bulan, $tahun);
    }
}
