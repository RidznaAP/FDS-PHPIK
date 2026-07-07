<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluasi extends Model
{
    protected $table = 'evaluasis'; // Paksa nama tabel yang benar
    protected $fillable = [
        'perencanaan_id', 'kesimpulan', 'tanggal_evaluasi',
        'evaluator', 'prevalensi', 'insidensi',
        'rekomendasi', 'status_warna',
    ];

    protected $casts = [
        'tanggal_evaluasi' => 'date',
    ];

    // Relasi ke Perencanaan
    public function perencanaan()
    {
        return $this->belongsTo(Perencanaan::class);
    }

    // Helper: warna badge berdasarkan status
    public function getWarnaAttribute(): string
    {
        return match($this->status_warna) {
            'hijau' => 'success',
            'kuning' => 'warning',
            'merah' => 'danger',
            default => 'secondary',
        };
    }

    // Helper: label kesimpulan
    public function getLabelKesimpulanAttribute(): string
    {
        return match($this->kesimpulan) {
            'Bebas HPIK' => '🟢 Bebas HPIK',
            'Waspada' => '🟡 Waspada',
            'Positif HPIK' => '🔴 Positif HPIK',
            default => $this->kesimpulan,
        };
    }
}
