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
        Schema::table('dokumen_seminar', function (Blueprint $table) {
            $table->string('nama_file')->nullable()->change();
            $table->string('path_file')->nullable()->change();
            $table->text('link_drive')->nullable()->after('path_file');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dokumen_seminar', function (Blueprint $table) {
            $table->string('nama_file')->nullable(false)->change();
            $table->string('path_file')->nullable(false)->change();
            $table->dropColumn('link_drive');
        });
    }
};
