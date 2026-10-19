<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nilai_mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('enrollment_mahasiswa_angkatan_kelas_id');
            $table->unsignedBigInteger('mata_kuliah_angkatan_kelas_id');
            $table->float('nilai_angka')->default(85.50);
            $table->string('nilai_huruf')->default('A');
            $table->float('bobot_nilai')->default(4.00);
            $table->string('status_kelulusan')->default('Lulus');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nilai_mahasiswa');
    }
};