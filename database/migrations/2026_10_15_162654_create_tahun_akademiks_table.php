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
            $table->string('tahun', 20);
            $table->string('semester', 20)->default('Ganjil');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['tahun', 'semester'], 'tahun_akademik_tahun_semester_unique');
        });
    }

    public function down(): void
    {
        // Menggunakan nama tabel asli tanpa 's'
        Schema::dropIfExists('tahun_akademik');
    }
};
