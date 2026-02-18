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
        Schema::create('perencanaans', function (Blueprint $table) {
    $table->id();
    $table->string('provinsi');
    $table->string('kab_kota');
    $table->string('jenis_mp');
    $table->text('jenis_hpik'); // Kita pakai text dulu agar bisa simpan banyak nama penyakit
    $table->string('kemampuan_uji_upt');
    $table->string('metode_pengujian');
    $table->string('lab_uji');
    $table->integer('target_uji');
    $table->integer('tw1')->default(0);
    $table->integer('tw2')->default(0);
    $table->integer('tw3')->default(0);
    $table->integer('tw4')->default(0);
    $table->integer('total_pengujian');
    $table->enum('status', ['draft', 'waiting', 'approved'])->default('draft');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perencanaans');
    }
};