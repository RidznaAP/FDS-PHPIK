<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Perencanaan;

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
        'email',
        'password',
        'plain_password',
        'role',
        'upt_asal',
        'parent_id',
    ];

    /**
     * Relasi ke Koordinator (BBKHIT)
     */
    public function coordinator()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    /**
     * Relasi ke Unit-Unit di bawah koordinasi (untuk BBKHIT)
     */
    public function units()
    {
        return $this->hasMany(User::class, 'parent_id');
    }

    /** Relasi ke Perencanaan milik user ini */
    public function perencanaan()
    {
        return $this->hasMany(Perencanaan::class);
    }

    // Helper methods untuk cek role (Normalized)
    public function isBkhit(): bool
    {
        return trim(strtolower($this->role)) === 'bkhit';
    }

    // Alias backward-compat
    public function isUpt(): bool
    {
        return $this->isBkhit();
    }

    public function isBbkhit(): bool
    {
        return trim(strtolower($this->role)) === 'bbkhit';
    }

    public function isPusat(): bool
    {
        return trim(strtolower($this->role)) === 'pusat';
    }

    public function isDeveloper(): bool
    {
        return trim(strtolower($this->role)) === 'developer';
    }

    /**
     * Super-admin check — developer memiliki akses penuh setara atau melebihi pusat.
     */
    public function isSuperAdmin(): bool
    {
        return $this->isDeveloper();
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'plain_password', // Sembunyikan dari response JSON/API
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
    ];
}
