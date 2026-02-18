<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelaksanaan extends Model
{
    // Ini wajib ada agar data dari form bisa masuk
    protected $guarded = [];

    // Relasi balik ke Perencanaan
    public function perencanaan()
    {
        return $this->belongsTo(Perencanaan::class);
    }
}