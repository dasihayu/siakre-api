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
        Schema::create('nilai_mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mata_kuliah_angkatan_kelas_id')->constrained('mata_kuliah_angkatan_kelas')->onDelete('cascade');
            $table->foreignId('mahasiswa_id')->constrained('data_mahasiswa')->onDelete('cascade');
            $table->unsignedTinyInteger('semester')->default(1);
            $table->decimal('nilai_angka', 5, 2)->nullable();
            $table->string('nilai_huruf', 5)->nullable();
            $table->decimal('bobot', 3, 2)->nullable();
            $table->string('link_nilai', 2048)->nullable();
            $table->string('link_evidence', 2048)->nullable();
            $table->timestamps();

            $table->unique(['mata_kuliah_angkatan_kelas_id', 'mahasiswa_id'], 'nilai_mk_ak_mhs_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai_mahasiswa');
    }
};
