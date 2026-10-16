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
        Schema::create('status_mahasiswa', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('id_enrollment_mahasiswa_angkatan_kelas')
                  ->constrained('enrollment_mahasiswa_angkatan_kelas')
                  ->onDelete('restrict');
            $table->string('status'); 
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status_mahasiswa');
    }
};
