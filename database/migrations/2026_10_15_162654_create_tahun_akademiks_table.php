<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tahun_akademik', function (Blueprint $table) {
            $table->id();
            $table->string('tahun', 20); // Contoh: "2026/2027"
            // Diubah menggunakan enum agar sesuai dengan pilihan tugas: Ganjil, Genap
            $table->enum('semester', ['Ganjil', 'Genap'])->default('Ganjil'); 
            $table->boolean('is_active')->default(false); // Default false agar tidak bentrok saat input baru
            $table->timestamps();

            $table->unique(['tahun', 'semester'], 'tahun_akademik_tahun_semester_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tahun_akademik');
    }
};