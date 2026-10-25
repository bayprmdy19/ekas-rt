<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warga extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_lengkap',
        'nik',
        'nomor_rumah',
        'no_hp',
        'status_tinggal',
    ];

    public function iuranWargas(): HasMany
    {
        return $this->hasMany(IuranWarga::class);
    }

    public function pengaduans(): HasMany
    {
        return $this->hasMany(Pengaduan::class);
    }
}
