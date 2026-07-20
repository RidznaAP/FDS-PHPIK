<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class RegulasiInformasi extends Model
{
    protected $table = 'regulasi_informasi';

    protected $fillable = [
        'user_id',
        'judul',
        'deskripsi',
        'tipe_lampiran',
        'nama_file',
        'path_file',
        'ukuran_file',
    ];

    /**
     * Relasi ke user yang mengunggah
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Auto-hapus file fisik saat record dihapus
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($regulasi) {
            if ($regulasi->path_file) {
                Storage::disk('public')->delete($regulasi->path_file);
            }
        });
    }

    /**
     * Cek apakah lampiran berupa foto
     */
    public function isFoto(): bool
    {
        return $this->tipe_lampiran === 'foto';
    }

    /**
     * Cek apakah lampiran berupa dokumen
     */
    public function isDokumen(): bool
    {
        return $this->tipe_lampiran === 'dokumen';
    }

    /**
     * Helper: URL public file jika ada
     */
    public function fileUrl(): ?string
    {
        return $this->path_file ? Storage::disk('public')->url($this->path_file) : null;
    }
}
