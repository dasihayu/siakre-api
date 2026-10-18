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
        Schema::create('evidence_prestasi_mahasiswa', function (Blueprint $table) {
            $table->id(); // PK, int
            $table->foreignId('id_prestasi_mahasiswa')->constrained('prestasi_mahasiswa')->onDelete('restrict'); // FK ke prestasi_mahasiswa
            $table->string('evidence'); // string, mandatory
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evidence_prestasi_mahasiswa');
    }
};
