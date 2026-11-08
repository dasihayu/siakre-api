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
        Schema::create('cpl_indikator', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_cpl')->constrained('cpl')->onDelete('cascade');
            $table->foreignId('id_indikator_lam')->constrained('indikator_lam')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['id_cpl', 'id_indikator_lam']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cpl_indikator');
    }
};
