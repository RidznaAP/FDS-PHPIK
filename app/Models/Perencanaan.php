<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perencanaan extends Model
{
    protected $fillable = [
        'user_id', 'provinsi', 'kab_kota', 'jenis_mp', 'jenis_hpik',
        'kemampuan_uji_upt', 'metode_pengujian', 'lab_uji', 'target_uji',
        'tw1', 'tw2', 'tw3', 'tw4', 'total_pengujian',
        'rencana_lokasi', 'rencana_jumlah_sampel', 'rencana_metode_sampling',
        'status', 'alasan_penolakan',
    ];

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
