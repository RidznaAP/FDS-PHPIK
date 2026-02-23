<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perencanaan extends Model
{
    protected $guarded = [];

    // Relasi ke User yang membuat
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pelaksanaans()
    {
        return $this->hasMany(Pelaksanaan::class);
    }

    public function evaluasi()
    {
        return $this->hasOne(Evaluasi::class);
    }
}
