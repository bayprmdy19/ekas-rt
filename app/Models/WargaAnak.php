<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WargaAnak extends Model
{
    use HasFactory;

    protected $table = 'warga_anaks';

    protected $fillable = [
        'warga_id',
        'kartu_keluarga_id',
        'nama_anak',
        'umur',
        'tempat_lahir',
        'tanggal_lahir',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    /**
     * Relasi ke data Warga (Orang Tua / Kepala Keluarga)
     */
    public function warga(): BelongsTo
    {
        return $this->belongsTo(Warga::class);
    }

    /**
     * Relasi opsional ke Kartu Keluarga
     */
    public function kartuKeluarga(): BelongsTo
    {
        return $this->belongsTo(KartuKeluarga::class);
    }
}

