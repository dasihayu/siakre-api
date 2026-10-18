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
        Schema::create('enrollment_mahasiswa_angkatan_kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_mahasiswa')->constrained('data_mahasiswa')->onDelete('restrict');
            $table->foreignId('angkatan_kelas_id')->constrained('angkatan_kelas')->onDelete('restrict');
            $table->foreignId('prodi_id')->constrained('prodi')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollment_kelas');
    }
};
