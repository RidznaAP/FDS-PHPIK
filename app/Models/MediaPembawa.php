<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaPembawa extends Model
{
    protected $guarded = [];

    protected $casts = [
        'aktif' => 'boolean',
    ];

    // Scope: hanya yang aktif (untuk dropdown)
    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }
}
