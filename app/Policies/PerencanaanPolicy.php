<?php

namespace App\Policies;

use App\Models\Perencanaan;
use App\Models\User;

class PerencanaanPolicy
{
    /**
     * Pusat dan Developer bisa melakukan apapun tanpa pembatasan.
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->isPusat() || $user->isDeveloper()) {
            return true;
        }
        return null;
    }

    /**
     * Semua user yang terotentikasi bisa melihat daftar.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * BKHIT hanya bisa lihat miliknya.
     * BBKHIT bisa lihat miliknya + BKHIT di bawah koordinasinya.
     */
    public function view(User $user, Perencanaan $perencanaan): bool
    {
        if ($user->isBkhit()) {
            return $perencanaan->user_id === $user->id;
        }

        if ($user->isBbkhit()) {
            $owner = User::find($perencanaan->user_id);
            return $perencanaan->user_id === $user->id
                || ($owner && $owner->parent_id === $user->id);
        }

        return false;
    }

    /**
     * Semua role bisa membuat perencanaan baru.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Hanya pemilik yang bisa edit, dan hanya saat status Draft atau Rejected.
     * BBKHIT koordinator juga bisa edit data BKHIT bawahannya.
     */
    public function update(User $user, Perencanaan $perencanaan): bool
    {
        if (!in_array($perencanaan->status, ['draft', 'rejected'])) {
            return false;
        }

        // Pemilik data
        if ($perencanaan->user_id === $user->id) {
            return true;
        }

        // BBKHIT koordinator bisa edit data BKHIT bawahannya
        if ($user->isBbkhit()) {
            $owner = User::find($perencanaan->user_id);
            return $owner && $owner->parent_id === $user->id;
        }

        return false;
    }

    /**
     * Hanya pemilik yang bisa hapus, dan hanya saat status Draft atau Rejected.
     */
    public function delete(User $user, Perencanaan $perencanaan): bool
    {
        return $perencanaan->user_id === $user->id
            && in_array($perencanaan->status, ['draft', 'rejected']);
    }

    /**
     * Submit: pemilik bisa ajukan jika status draft atau rejected.
     */
    public function submit(User $user, Perencanaan $perencanaan): bool
    {
        return $perencanaan->user_id === $user->id
            && in_array($perencanaan->status, ['draft', 'rejected']);
    }

    /**
     * Approve: BBKHIT hanya untuk wilayah koordinasinya (BKHIT yg parent_id-nya = id BBKHIT).
     * Juga izinkan BBKHIT approve data miliknya sendiri.
     */
    public function approve(User $user, Perencanaan $perencanaan): bool
    {
        if (!($user->isBbkhit() || $user->isPusat() || $user->isDeveloper())) {
            return false;
        }

        if ($user->isBbkhit()) {
            // Milik sendiri
            if ($perencanaan->user_id === $user->id) {
                return true;
            }
            // Milik BKHIT bawahan koordinasinya
            $owner = User::find($perencanaan->user_id);
            return $owner && $owner->parent_id === $user->id;
        }

        return true;
    }

    /**
     * Reject: sama seperti approve.
     */
    public function reject(User $user, Perencanaan $perencanaan): bool
    {
        return $this->approve($user, $perencanaan);
    }
}
