<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DokumenSeminar extends Model
{
    protected $table = 'dokumen_seminar';

    protected $fillable = [
        'user_id',
        'jenis_modul',
        'judul',
        'nama_file',
        'path_file',
        'ukuran_file',
        'keterangan',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Boot function untuk melempar event pada Eloquent.
     * Membersihkan file fisik di storage ketika baris dihapus dari database.
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($dokumen) {
            if ($dokumen->path_file) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($dokumen->path_file);
            }
        });
    }
}
