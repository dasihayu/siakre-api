<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('angkatan', function (Blueprint $table) {
            $table->id();
            $table->integer('angkatan')->unique();
            $table->timestamps();
});
    }

    public function down(): void
    {
        // Menggunakan nama tabel asli tanpa 's'
        Schema::dropIfExists('angkatan');
    }
};