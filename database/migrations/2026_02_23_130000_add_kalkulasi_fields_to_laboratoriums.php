<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laboratoriums', function (Blueprint $table) {
            // Komponen kalkulasi Prevalensi & Insidensi (sesuai pedoman HPIK)
            $table->integer('jumlah_ikan_terinfeksi')->nullable()->after('insidensi');
            $table->integer('jumlah_sampel_diperiksa')->nullable()->after('jumlah_ikan_terinfeksi');
            $table->integer('jumlah_kolam_uji')->nullable()->after('jumlah_sampel_diperiksa');
            $table->integer('periode_pengamatan')->nullable()->after('jumlah_kolam_uji'); // dalam hari
        });
    }

    public function down(): void
    {
        Schema::table('laboratoriums', function (Blueprint $table) {
            $table->dropColumn([
                'jumlah_ikan_terinfeksi',
                'jumlah_sampel_diperiksa',
                'jumlah_kolam_uji',
                'periode_pengamatan',
            ]);
        });
    }
};
