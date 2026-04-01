<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('laboratoriums', function (Blueprint $table) {
            $table->string('panjang')->nullable();
            $table->string('berat')->nullable();
            $table->string('asal_benih_induk')->nullable();
            $table->string('padat_tebar')->nullable();
            $table->text('gejala_klinis')->nullable();
            $table->string('jumlah_kematian')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laboratoriums', function (Blueprint $table) {
            $table->dropColumn([
                'panjang',
                'berat',
                'asal_benih_induk',
                'padat_tebar',
                'gejala_klinis',
                'jumlah_kematian'
            ]);
        });
    }
};
