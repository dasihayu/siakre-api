<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mata_kuliah', function (Blueprint $table) {
            $table->unsignedTinyInteger('semester')->nullable();
            $table->string('jenis', 20)->nullable();
            $table->unsignedTinyInteger('sks_teori')->nullable();
            $table->unsignedTinyInteger('sks_praktik')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('mata_kuliah', function (Blueprint $table) {
            $table->dropColumn(['semester', 'jenis', 'sks_teori', 'sks_praktik']);
        });
    }
};
