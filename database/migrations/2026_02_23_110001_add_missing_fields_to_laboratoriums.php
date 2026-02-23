<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laboratoriums', function (Blueprint $table) {
            // Kolom 11-14 — Hasil per jenis patogen (+/-/NT)
            // NT = Not Tested (tidak diuji untuk target tersebut)
            $table->enum('hasil_parasit', ['+', '-', 'NT'])->default('NT')->after('hasil_uji');
            $table->enum('hasil_bakteri', ['+', '-', 'NT'])->default('NT')->after('hasil_parasit');
            $table->enum('hasil_virus',   ['+', '-', 'NT'])->default('NT')->after('hasil_bakteri');
            $table->enum('hasil_jamur',   ['+', '-', 'NT'])->default('NT')->after('hasil_virus');

            // Kolom 15-16 — Prevalensi & Insidensi (%)
            $table->decimal('prevalensi', 5, 2)->nullable()->after('hasil_jamur');
            $table->decimal('insidensi',  5, 2)->nullable()->after('prevalensi');
        });
    }

    public function down(): void
    {
        Schema::table('laboratoriums', function (Blueprint $table) {
            $table->dropColumn([
                'hasil_parasit', 'hasil_bakteri', 'hasil_virus',
                'hasil_jamur', 'prevalensi', 'insidensi',
            ]);
        });
    }
};
