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
        Schema::create('data_mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->integer('nim')->unique(); 
            $table->string('nama_mahasiswa'); // nama_mahasiswa string
            $table->integer('tahun_masuk'); // tahun_masuk date
            $table->string('jenis_pendaftaran'); // jenis_pendaftaran string
            $table->date('tanggal_kelulusan')->nullable(); // tanggal_kelulusan date
            $table->integer('masa_studi_bulan')->default(48); // masa_studi_bulan int, default 48
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_mahasiswa');
    }
};
