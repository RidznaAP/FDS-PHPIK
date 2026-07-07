<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Langkah 1: ubah ke VARCHAR dulu agar bisa dimodifikasi
        DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(20) NOT NULL DEFAULT 'bkhit'");

        // Langkah 2: terapkan enum baru dengan tambahan 'developer'
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('bkhit','bbkhit','pusat','developer') NOT NULL DEFAULT 'bkhit'");
    }

    public function down(): void
    {
        // Pastikan tidak ada user developer sebelum rollback
        DB::table('users')->where('role', 'developer')->update(['role' => 'pusat']);

        DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(20) NOT NULL DEFAULT 'bkhit'");
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('bkhit','bbkhit','pusat') NOT NULL DEFAULT 'bkhit'");
    }
};
