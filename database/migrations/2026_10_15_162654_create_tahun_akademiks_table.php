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
            $table->integer('tahun');
            $table->timestamps();
        }); 
    }

    public function down(): void
    {
        // Menggunakan nama tabel asli tanpa 's'
        Schema::dropIfExists('tahun_akademik');
    }
};