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
        Schema::create('cpl', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_kurikulum')->constrained('kurikulum')->onDelete('restrict'); // FK ke tabel kurikulum dengan aturan restrict
            $table->string('kode_cpl')->unique(); // Sesuai flowchart: Cek Kode CPL Duplikat
            $table->string('deskripsi_cpl');
            $table->string('kategori')->default('Umum')->nullable(); // Default sesuai ERD
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cpls');
    }
};
