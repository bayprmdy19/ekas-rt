<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterIuran extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_iuran',
        'nominal',
        'tanggal_mulai',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
        'tanggal_mulai' => 'date',
    ];
}
