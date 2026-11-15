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
        Schema::create('riwayat_status_mahasiswa', function (Blueprint $table) {
            $table->id();
            // Mengarah ke nama tabel asli yang benar: 'data_mahasiswa'
            $table->foreignId('id_mahasiswa')->constrained('data_mahasiswa')->onDelete('cascade');
            // Diubah dari 'master_status_mahasiswa' menjadi 'status_mahasiswa'
            $table->foreignId('id_status')->constrained('status_mahasiswa')->onDelete('restrict');
            $table->foreignId('id_tahun_akademik')->constrained('tahun_akademik')->onDelete('restrict');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_status_mahasiswa');
    }
};