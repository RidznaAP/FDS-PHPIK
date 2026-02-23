<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pelaksanaans', function (Blueprint $table) {
            // Kolom 2 — Tanggal Pemantauan
            $table->date('tanggal_pemantauan')->nullable()->after('perencanaan_id');

            // Kolom 4 — Jenis Ikan (nama lokal + nama latin)
            $table->string('jenis_ikan')->nullable()->after('tanggal_pemantauan');
            $table->string('nama_latin')->nullable()->after('jenis_ikan');

            // Kolom 5-6 — Ukuran Sampel
            $table->decimal('panjang_cm', 5, 2)->nullable()->after('nama_latin');
            $table->decimal('berat_gram', 8, 2)->nullable()->after('panjang_cm');

            // Kolom 7 — Asal Benih/Induk
            $table->string('asal_benih_induk')->nullable()->after('berat_gram');

            // Kolom 8 — Padat Tebar (ekor/m2 atau ekor/m3)
            $table->integer('padat_tebar')->nullable()->after('asal_benih_induk');

            // Kolom 9 & 10 — Kondisi Ikan
            $table->text('gejala_klinis')->nullable()->after('padat_tebar');
            $table->integer('jumlah_kematian')->nullable()->default(0)->after('gejala_klinis');
        });
    }

    public function down(): void
    {
        Schema::table('pelaksanaans', function (Blueprint $table) {
            $table->dropColumn([
                'tanggal_pemantauan', 'jenis_ikan', 'nama_latin',
                'panjang_cm', 'berat_gram', 'asal_benih_induk',
                'padat_tebar', 'gejala_klinis', 'jumlah_kematian',
            ]);
        });
    }
};
