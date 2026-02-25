<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_pembawas', function (Blueprint $table) {
            $table->id();
            $table->string('nama');           // Nama lokal (Udang Vaname, Lele, dll)
            $table->string('nama_latin')->nullable();  // Nama ilmiah
            $table->text('keterangan')->nullable();    // Deskripsi / catatan
            $table->boolean('aktif')->default(true);  // Apakah aktif ditampilkan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_pembawas');
    }
};
