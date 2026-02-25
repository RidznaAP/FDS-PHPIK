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
        'email',
        'password',
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
    ];
}
