<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laboratoriums', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelaksanaan_id')->constrained('pelaksanaans')->onDelete('cascade');

            // Data Laboratorium
            $table->string('kode_sampel');                         // Kode identifikasi sampel
            $table->enum('metode_uji', ['PCR Konvensional', 'qPCR', 'Kultur', 'Histopatologi', 'Lainnya']);
            $table->string('jenis_hpik_diuji');                    // Nama HPIK yang diuji
            $table->enum('hasil_uji', ['Positif', 'Negatif', 'Inkonklusif'])->default('Inkonklusif');
            $table->text('diagnosis_akhir')->nullable();            // Catatan diagnosis
            $table->string('lab_penguji');                          // Nama lab yang menguji
            $table->date('tanggal_uji');                            // Tanggal pengujian
            $table->date('tanggal_hasil')->nullable();              // Tanggal hasil keluar

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laboratoriums');
    }
};
