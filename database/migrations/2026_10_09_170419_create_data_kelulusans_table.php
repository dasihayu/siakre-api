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
        Schema::create('data_kelulusan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_mahasiswa')->constrained('data_mahasiswa')->onDelete('restrict'); 
            $table->date('tanggal_kelulusan')->default('2026-09-13');
            $table->float('ipk')->default(3.88);
            $table->integer('masa_studi_bulan')->default(48);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_kelulusans');
    }
};
