<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekeningRt extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_bank',
        'nomor_rekening',
        'atas_nama',
        'jenis',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
