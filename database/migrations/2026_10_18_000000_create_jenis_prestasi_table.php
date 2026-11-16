<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenis_prestasi', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique(); // Kode unik jenis prestasi
            $table->string('nama'); // Nama jenis prestasi
            $table->enum('kategori', ['Akademik', 'Non-Akademik']); // Kategori baku
            $table->enum('tingkat', ['Internasional', 'Nasional', 'Regional', 'Lokal']); // Tingkat baku
            $table->boolean('is_active')->default(true); // Status keaktifan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jenis_prestasi');
    }
};