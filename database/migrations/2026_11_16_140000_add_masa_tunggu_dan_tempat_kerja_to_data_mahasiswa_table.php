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
        Schema::table('data_mahasiswa', function (Blueprint $table) {
            $table->integer('masa_tunggu_bulan')->nullable()->after('masa_studi_bulan')->comment('Lama menunggu pekerjaan pertama dalam bulan');
            $table->string('tempat_kerja')->nullable()->after('masa_tunggu_bulan')->comment('Nama perusahaan tempat bekerja saat ini');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_mahasiswa', function (Blueprint $table) {
            $table->dropColumn(['masa_tunggu_bulan', 'tempat_kerja']);
        });
    }
};
