<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->string('kelas', 50);
            $table->timestamps();
            });
    }

    public function down(): void
    {
        // Menggunakan nama tabel asli tanpa 's'
        Schema::dropIfExists('kelas');
    }
};