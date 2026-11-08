<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_mata_kuliah')->constrained('mata_kuliah')->cascadeOnDelete();
            $table->string('versi', 50);
            $table->unsignedSmallInteger('tahun_berlaku');
            $table->string('dokumen_url', 2048)->nullable();
            $table->string('status', 20)->default('Draft');
            $table->timestamps();

            $table->index(['id_mata_kuliah', 'tahun_berlaku']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rps');
    }
};
