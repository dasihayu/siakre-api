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
        Schema::create('kegiatan_mahasiswa', function (Blueprint $table) {
            $table->id(); // PK int
            $table->foreignId('mahasiswa_id')->constrained('data_mahasiswa')->onDelete('restrict');
            $table->foreignId('tahun_akademik_id')->constrained('tahun_akademik')->onDelete('restrict');
            $table->string('nama_kegiatan'); 
            $table->string('jenis_kegiatan');
            $table->string('peran'); 
            $table->string('tingkat');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
            $table->string('penyelenggara');
            $table->string('status_validasi')->default('Pending');
            $table->timestamps();
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kegiatan_mahasiswa');
    }
};
