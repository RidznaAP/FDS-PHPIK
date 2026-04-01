<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laboratoriums', function (Blueprint $table) {
            $table->string('hasil_parasit', 25)->change();
            $table->string('hasil_bakteri', 25)->change();
            $table->string('hasil_virus', 25)->change();
            $table->string('hasil_jamur', 25)->change();
        });
    }

    public function down(): void
    {
        Schema::table('laboratoriums', function (Blueprint $table) {
            $table->string('hasil_parasit', 10)->change();
            $table->string('hasil_bakteri', 10)->change();
            $table->string('hasil_virus', 10)->change();
            $table->string('hasil_jamur', 10)->change();
        });
    }
};
