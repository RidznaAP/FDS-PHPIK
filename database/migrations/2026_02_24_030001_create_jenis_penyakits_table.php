<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenis_penyakits', function (Blueprint $table) {
            $table->id();
            $table->string('nama');                    // Nama penyakit (White Spot, dll)
            $table->string('singkatan')->nullable();   // Contoh: WSSV, KHV
            $table->string('golongan')->nullable();    // Virus / Bakteri / Parasit / Jamur
            $table->text('keterangan')->nullable();    // Deskripsi / catatan
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jenis_penyakits');
    }
};
