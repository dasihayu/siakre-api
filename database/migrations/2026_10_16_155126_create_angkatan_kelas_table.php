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
        Schema::create('angkatan_kelas', function (Blueprint $table) {
            $table->id();
            // Foreign Keys mandatory
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('restrict');
            $table->foreignId('angkatan_id')->constrained('angkatan')->onDelete('restrict');
            $table->foreignId('prodi_id')->constrained('prodi')->onDelete('restrict');
            $table->foreignId('tahun_akademik_id')->constrained('tahun_akademik')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('angkatan_kelas');
    }
};
