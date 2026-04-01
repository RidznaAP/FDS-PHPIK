<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laboratoriums', function (Blueprint $table) {
            $table->dropColumn('kelompok_patogen');
            $table->string('hasil_parasit', 10)->default('NT')->after('diagnosis_akhir');
            $table->string('hasil_bakteri', 10)->default('NT')->after('hasil_parasit');
            $table->string('hasil_virus', 10)->default('NT')->after('hasil_bakteri');
            $table->string('hasil_jamur', 10)->default('NT')->after('hasil_virus');
        });
    }

    public function down(): void
    {
        Schema::table('laboratoriums', function (Blueprint $table) {
            $table->string('kelompok_patogen')->nullable()->after('diagnosis_akhir');
            $table->dropColumn(['hasil_parasit', 'hasil_bakteri', 'hasil_virus', 'hasil_jamur']);
        });
    }
};
