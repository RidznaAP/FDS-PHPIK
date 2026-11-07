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
            // Ubah enum status untuk menambah nilai 'rejected'
            $table->enum('status', ['draft', 'waiting', 'approved', 'rejected'])
                  ->default('draft')
                  ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perencanaans', function (Blueprint $table) {
            $table->enum('status', ['draft', 'waiting', 'approved'])
                  ->default('draft')
                  ->change();
        });
    }
};
