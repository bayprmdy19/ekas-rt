<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'no_hp',
        'no_kk',
        'email',
        'password',
        'role',
        'is_profile_complete',
    ];

    /**
     * Cek apakah user adalah admin atau pengurus
     */
    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'pengurus']);
    }

    /**
     * Cek apakah user adalah warga
     */
    public function isWarga(): bool
    {
        return $this->role === 'warga';
    }

    /**
     * Data Profil Warga Terhubung
     */
    public function warga(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Warga::class);
    }

    /**
     * Data Kartu Keluarga & Dokumen Warga
     */
    public function kartuKeluarga(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(KartuKeluarga::class);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_profile_complete' => 'boolean',
    ];
}
