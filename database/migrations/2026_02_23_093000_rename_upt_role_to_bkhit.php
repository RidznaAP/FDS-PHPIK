<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Langkah 1: ubah kolom ke string biasa dulu (lepas enum constraint)
        DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(20) NOT NULL DEFAULT 'bkhit'");

        // Langkah 2: update semua user yang role-nya 'upt' jadi 'bkhit'
        DB::table('users')->where('role', 'upt')->update(['role' => 'bkhit']);

        // Langkah 3: terapkan enum baru yang sudah tidak punya 'upt'
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('bkhit', 'bbkhit', 'pusat') NOT NULL DEFAULT 'bkhit'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(20) NOT NULL DEFAULT 'upt'");
        DB::table('users')->where('role', 'bkhit')->update(['role' => 'upt']);
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('upt', 'bbkhit', 'pusat') NOT NULL DEFAULT 'upt'");
    }
};
