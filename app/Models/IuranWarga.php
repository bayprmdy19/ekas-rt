<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IuranWarga extends Model
{
    use HasFactory;

    protected $fillable = [
        'warga_id',
        'jenis_iuran',
        'bulan_tahun',
        'jumlah',
        'status',
        'metode_pembayaran',
        'bukti_bayar',
        'tanggal_bayar',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_bayar' => 'date',
        'jumlah' => 'decimal:2',
    ];

    public function warga(): BelongsTo
    {
        return $this->belongsTo(Warga::class);
    }
}
