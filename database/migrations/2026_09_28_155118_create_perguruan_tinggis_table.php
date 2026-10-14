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
        Schema::create('perguruan_tinggis', function (Blueprint $table) {
            $table->id(); // Primary Key int
            $table->string('kode_pt')->default('PT001'); // Mandatory dengan default 'PT001'
            $table->string('nama_pt')->default('Polines'); // Mandatory dengan default 'Polines'
            $table->string('alamat')->nullable()->default('Jakarta'); // Default 'Jakarta'
            $table->string('pimpinan')->nullable()->default('Dyonisius Beti'); // Default 'Dyonisius Beti'
            $table->string('visi')->nullable(); // 
            $table->string('misi')->nullable(); //
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perguruan_tinggis');
    }
};
