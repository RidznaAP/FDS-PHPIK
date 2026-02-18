<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perencanaan extends Model
{
    protected $guarded = []; // Agar data bisa disimpan

    // Tambahkan ini
    public function pelaksanaans()
    {
        return $this->hasMany(Pelaksanaan::class);
    }
}
