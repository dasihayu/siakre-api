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
        Schema::create('upps', function (Blueprint $table) {
            $table->id(); // PK, int
            $table->foreignId('id_pt')->constrained('perguruan_tinggis')->onDelete('restrict'); // FK ke perguruan_tinggi
            $table->string('kode_upps'); // string, mandatory
            $table->string('nama_upps'); // string, mandatory
            $table->string('pimpinan_upps')->nullable(); // string, opsional
            $table->foreignId('prodi_id')->constrained('prodi')->onDelete('restrict'); // FK ke prodi
            $table->string('jenis'); // string, mandatory
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('upps');
    }
};
