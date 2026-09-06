<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('perencanaans', function (Blueprint $table) {
            // Tambahkan kolom tahun (setelah user_id)
            $table->unsignedSmallInteger('tahun')->nullable()->after('user_id');
            $table->index('tahun');
        });

        // Isi tahun existing data dari created_at
        DB::statement('UPDATE perencanaans SET tahun = YEAR(created_at) WHERE tahun IS NULL');

        // Setelah backfill, buat default-nya tahun ini
        Schema::table('perencanaans', function (Blueprint $table) {
            $table->unsignedSmallInteger('tahun')->default(date('Y'))->change();
        });
    }

    public function down(): void
    {
        Schema::table('perencanaans', function (Blueprint $table) {
            $table->dropIndex(['tahun']);
            $table->dropColumn('tahun');
        });
    }
};
