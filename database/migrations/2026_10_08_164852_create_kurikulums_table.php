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
        Schema::create('kurikulum', function (Blueprint $table) {
            $table->id();
            // FK ke tabel prodi dengan aturan restrict
            $table->foreignId('id_prodi')->constrained('prodi')->onDelete('restrict');
            $table->string('nama_kurikulum');
            $table->integer('berlaku_sampai'); // Pakai integer karena isinya tahun (misal: 2028)
            $table->string('sk_kurikulum')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kurikulums');
    }
};
