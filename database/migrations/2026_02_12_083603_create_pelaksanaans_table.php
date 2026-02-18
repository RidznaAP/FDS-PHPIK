<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('pelaksanaans', function (Blueprint $table) {
            $table->id();
            // Relasi ke tabel perencanaan (Kolom 1-12)
            $table->foreignId('perencanaan_id')->constrained('perencanaans')->onDelete('cascade');

            // Data Lapangan (Kolom 13-15)
            $table->string('lokasi_pengambilan_sampel'); // Contoh: Tambak Udang A
            $table->integer('jumlah_sampel');           // Jumlah riil diambil
            $table->string('metode_pengambilan_sampel'); // Acak/Selektif

            // Data untuk Peta (GIS)
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelaksanaans');
    }
};