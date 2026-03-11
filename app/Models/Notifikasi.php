<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    protected $table = 'notifications';

    protected $fillable = [
        'user_id',
        'tipe',
        'judul',
        'pesan',
        'url',
        'dibaca',
        'dari_user_id',
    ];

    protected $casts = [
        'dibaca' => 'boolean',
    ];

    /** Penerima notifikasi */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Pengirim notifikasi */
    public function dariUser()
    {
        return $this->belongsTo(User::class, 'dari_user_id');
    }

    /** Helper: kirim notifikasi ke satu atau banyak user */
    public static function kirim(array|int $userIds, string $tipe, string $judul, string $pesan, string $url = null, int $dariUserId = null): void
    {
        if (!is_array($userIds)) {
            $userIds = [$userIds];
        }
        foreach ($userIds as $uid) {
            self::create([
                'user_id'      => $uid,
                'tipe'         => $tipe,
                'judul'        => $judul,
                'pesan'        => $pesan,
                'url'          => $url,
                'dari_user_id' => $dariUserId,
            ]);
        }
    }
}
