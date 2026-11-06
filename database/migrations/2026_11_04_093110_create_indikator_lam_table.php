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
        Schema::create('indikator_lam', function (Blueprint $table) {
            $table->id();
            $table->string('kode_indikator')->unique();
            $table->string('nama_indikator');
            $table->text('deskripsi')->nullable();
            $table->string('standar')->nullable()->comment('Misal: C, D, C3.1 - kode standar LAM INFOKOM');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indikator_lam');
    }
};
