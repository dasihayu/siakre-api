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
        Schema::create('mata_kuliah_angkatan_kelas', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('enrollment_mata_kuliah_id')->constrained('enrollment_mata_kuliah')->onDelete('cascade'); 
            $table->foreignId('angkatan_kelas_id')->constrained('angkatan_kelas')->onDelete('cascade'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mata_kuliah_angkatan_kelas');
    }
};
