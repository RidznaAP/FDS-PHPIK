<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelaksanaan extends Model
{
    protected $guarded = [];

    protected $casts = [
        'tanggal_pemantauan' => 'date',
        'pengambil_sampel' => 'array',
    ];

    // Relasi balik ke Perencanaan
    public function perencanaan()
    {
        return $this->belongsTo(Perencanaan::class);
    }

    // Relasi ke Laboratorium (satu pelaksanaan punya satu hasil lab)
    public function laboratorium()
    {
        return $this->hasOne(Laboratorium::class);
    }
}