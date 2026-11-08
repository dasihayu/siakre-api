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
        Schema::table('cpl', function (Blueprint $table) {
            $table->dropUnique(['kode_cpl']);
            $table->unique(['id_kurikulum', 'kode_cpl']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cpl', function (Blueprint $table) {
            $table->dropUnique(['id_kurikulum', 'kode_cpl']);
            $table->unique('kode_cpl');
        });
    }
};
