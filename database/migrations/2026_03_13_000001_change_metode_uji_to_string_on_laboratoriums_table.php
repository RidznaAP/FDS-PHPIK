<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Change enum to string for metode_uji
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE laboratoriums MODIFY metode_uji VARCHAR(255) NULL");
    }

    public function down(): void
    {
        // Notice: Cannot easily revert back to tightly restricted enum without data loss risk if new values exist
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE laboratoriums MODIFY metode_uji ENUM('PCR Konvensional', 'qPCR', 'Kultur', 'Histopatologi', 'Lainnya')");
    }
};
