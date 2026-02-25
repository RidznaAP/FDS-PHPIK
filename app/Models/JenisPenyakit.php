<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisPenyakit extends Model
{
    protected $guarded = [];

    protected $casts = [
        'aktif' => 'boolean',
    ];

    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }
}
