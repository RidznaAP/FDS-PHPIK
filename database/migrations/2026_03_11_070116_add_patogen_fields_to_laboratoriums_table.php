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
        if (!Schema::hasColumn('laboratoriums', 'hasil_parasit')) {
            Schema::table('laboratoriums', function (Blueprint $table) {
                $table->string('hasil_parasit', 10)->default('NT')->after('jenis_hpik_diuji');
                $table->string('hasil_bakteri', 10)->default('NT')->after('hasil_parasit');
                $table->string('hasil_virus', 10)->default('NT')->after('hasil_bakteri');
                $table->string('hasil_jamur', 10)->default('NT')->after('hasil_virus');
            });
        }

        // Change enum to string if possible using modify
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE laboratoriums MODIFY hasil_uji VARCHAR(255) DEFAULT 'NIHIL'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laboratoriums', function (Blueprint $table) {
            $table->dropColumn(['hasil_parasit', 'hasil_bakteri', 'hasil_virus', 'hasil_jamur']);
        });
        
        // Revert to enum (may fail if there's other data)
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE laboratoriums MODIFY hasil_uji ENUM('Positif', 'Negatif', 'Inkonklusif') DEFAULT 'Inkonklusif'");
    }
};
