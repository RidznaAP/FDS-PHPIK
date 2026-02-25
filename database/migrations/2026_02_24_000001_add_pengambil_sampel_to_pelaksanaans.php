<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pelaksanaans', function (Blueprint $table) {
            // D. Identitas Pengambil Contoh Uji — disimpan sebagai JSON array nama petugas
            $table->json('pengambil_sampel')->nullable()->after('jumlah_kematian');
        });
    }

    public function down(): void
    {
        Schema::table('pelaksanaans', function (Blueprint $table) {
            $table->dropColumn('pengambil_sampel');
        });
    }
};
