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
        Schema::create('evidence_indikator', function (Blueprint $table) {
            $table->id();
            $table->enum('tipe_sumber', [
                'nilai_mahasiswa',
                'prestasi_mahasiswa',
                'kegiatan_mahasiswa',
            ]);
            $table->unsignedBigInteger('id_sumber');
            $table->foreignId('id_indikator_lam')->constrained('indikator_lam')->onDelete('cascade');
            $table->timestamps();

            $table->index(['tipe_sumber', 'id_sumber']);
            $table->unique(['tipe_sumber', 'id_sumber', 'id_indikator_lam'], 'evidence_indikator_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evidence_indikator');
    }
};
