<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Laboratorium extends Model
{
    protected $table = 'laboratoriums'; // Paksa nama tabel yang benar
    protected $guarded = [];

    protected $casts = [
        'tanggal_uji' => 'date',
        'tanggal_hasil' => 'date',
    ];

    // Relasi ke Pelaksanaan
    public function pelaksanaan()
    {
        return $this->belongsTo(Pelaksanaan::class);
    }

    // Akses langsung ke Perencanaan via Pelaksanaan
    public function perencanaan()
    {
        return $this->hasOneThrough(
            Perencanaan::class,
            Pelaksanaan::class,
            'id',              // FK di pelaksanaans
            'id',              // FK di perencanaans
            'pelaksanaan_id',  // Local key di laboratoriums
            'perencanaan_id'   // Local key di pelaksanaans
        );
    }
}
