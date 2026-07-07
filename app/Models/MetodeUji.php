<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetodeUji extends Model
{
    protected $table = 'metode_ujis';
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
