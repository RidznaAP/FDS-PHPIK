<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perencanaan_id')->constrained('perencanaans')->onDelete('cascade');

            // Hasil Evaluasi
            $table->enum('kesimpulan', ['Bebas HPIK', 'Waspada', 'Positif HPIK']);
            $table->enum('status_warna', ['hijau', 'kuning', 'merah']); // Untuk peta GIS
            $table->text('rekomendasi')->nullable();                     // Rekomendasi tindakan
            $table->text('catatan_evaluasi')->nullable();                // Catatan tambahan

            // Evaluator
            $table->string('evaluator');                                 // Nama tim teknis
            $table->date('tanggal_evaluasi');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluasis');
    }
};
