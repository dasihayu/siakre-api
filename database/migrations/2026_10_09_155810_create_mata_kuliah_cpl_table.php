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
        Schema::create('mata_kuliah_cpl', function (Blueprint $table) {
            $table->id();
            // Foreign keys to mata_kuliah and cpl tables
            $table->foreignId('id_mata_kuliah')->constrained('mata_kuliah')->onDelete('cascade');
            $table->foreignId('id_cpl')->constrained('cpl')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mata_kuliah_cpl');
    }
};
