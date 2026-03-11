<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // penerima notifikasi
            $table->string('tipe');           // e.g. 'upload_pelaporan', 'upload_evaluasi', 'perencanaan_waiting'
            $table->string('judul');          // judul singkat
            $table->text('pesan');            // isi notifikasi
            $table->string('url')->nullable();// link tujuan
            $table->boolean('dibaca')->default(false);
            $table->foreignId('dari_user_id')->nullable()->constrained('users')->nullOnDelete(); // pengirim
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
