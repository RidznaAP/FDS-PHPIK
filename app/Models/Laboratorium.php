<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Laboratorium extends Model
{
    protected $table = 'laboratoriums'; // Paksa nama tabel yang benar
    protected $fillable = [
        'pelaksanaan_id', 'kode_sampel', 'metode_uji', 'jenis_hpik_diuji',
        'hasil_uji', 'diagnosis_akhir', 'lab_penguji', 'nama_petugas_uji',
        'tanggal_uji', 'tanggal_hasil',
        'prevalensi', 'insidensi',
        'jumlah_ikan_terinfeksi', 'jumlah_sampel_diperiksa',
        'jumlah_kolam_uji', 'periode_pengamatan',
        'panjang', 'berat', 'asal_benih_induk',
        'padat_tebar', 'gejala_klinis', 'jumlah_kematian',
        'hasil_parasit', 'hasil_bakteri', 'hasil_virus', 'hasil_jamur',
    ];

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
