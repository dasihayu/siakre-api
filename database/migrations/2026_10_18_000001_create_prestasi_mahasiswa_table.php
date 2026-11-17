<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prestasi_mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_mahasiswa')->constrained('data_mahasiswa')->onDelete('cascade');
            $table->foreignId('id_jenis_prestasi')->nullable()->constrained('jenis_prestasi')->onDelete('set null');
            $table->string('nama_aktivitas');
            $table->integer('tahun');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prestasi_mahasiswa');
    }
};