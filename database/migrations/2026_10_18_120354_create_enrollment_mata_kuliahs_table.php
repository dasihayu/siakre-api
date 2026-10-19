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
        Schema::create('enrollment_mata_kuliah', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('tahun_akademik_id')->constrained('tahun_akademik')->onDelete('cascade'); 
            $table->foreignId('mata_kuliah_id')->constrained('mata_kuliah')->onDelete('cascade'); 
            $table->integer('dosen_id')->nullable(); 
            $table->integer('evidence')->nullable(); 
            $table->unsignedBigInteger('mata_kuliah_angkatan_kelas_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollment_mata_kuliah');
    }
};
