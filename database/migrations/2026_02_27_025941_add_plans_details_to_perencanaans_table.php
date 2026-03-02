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
        Schema::table('perencanaans', function (Blueprint $table) {
            $table->string('rencana_lokasi')->nullable()->after('total_pengujian');
            $table->integer('rencana_jumlah_sampel')->default(0)->after('rencana_lokasi');
            $table->string('rencana_metode_sampling')->nullable()->after('rencana_jumlah_sampel');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perencanaans', function (Blueprint $table) {
            $table->dropColumn(['rencana_lokasi', 'rencana_jumlah_sampel', 'rencana_metode_sampling']);
        });
    }
};
