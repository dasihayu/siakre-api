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
        Schema::create('prestasi_mahasiswa', function (Blueprint $table) {
            $table->id(); // PK, int
            $table->foreignId('id_mahasiswa')->constrained('data_mahasiswa')->onDelete('restrict'); // FK ke data_mahasiswa
            $table->string('nama_aktivitas'); // string, mandatory
            $table->integer('tahun'); // int, mandatory
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prestasi_mahasiswa');
    }
};
