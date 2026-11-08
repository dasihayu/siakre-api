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
        Schema::table('kurikulum', function (Blueprint $table) {
            $table->integer('tahun_mulai')
                ->nullable()
                ->after('nama_kurikulum');
            $table->enum('status', ['Aktif', 'Arsip'])
                ->default('Aktif')
                ->after('berlaku_sampai');
            $table->string('dokumen_url')
                ->nullable()
                ->after('status');
            $table->text('profil_lulusan')
                ->nullable()
                ->after('dokumen_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kurikulum', function (Blueprint $table) {
            $table->dropColumn([
                'tahun_mulai',
                'status',
                'dokumen_url',
                'profil_lulusan',
            ]);
        });
    }
};

