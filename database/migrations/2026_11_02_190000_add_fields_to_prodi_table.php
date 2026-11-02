<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('prodi', function (Blueprint $table) {
            $table->enum('jenjang', ['D1', 'D2', 'D3', 'D4', 'S1', 'S2', 'S3'])
                ->default('D4')
                ->after('nama_prodi');
            $table->string('sk_pendirian')
                ->nullable()
                ->after('jenjang');
            $table->date('tanggal_sk_pendirian')
                ->nullable()
                ->after('sk_pendirian');
            $table->enum('status_akreditasi', ['Terakreditasi', 'Terakreditasi Unggul'])
                ->nullable()
                ->after('tanggal_sk_pendirian');
            $table->date('masa_berlaku_akreditasi')
                ->nullable()
                ->after('status_akreditasi');
            $table->foreignId('id_upps')
                ->nullable()
                ->after('masa_berlaku_akreditasi')
                ->constrained('upps')
                ->onDelete('restrict');
        });

        // Migrasi data existing jika ada
        DB::table('prodi')->whereNull('jenjang')->update(['jenjang' => 'D4']);
        DB::table('prodi')->whereNull('status_akreditasi')->whereNotNull('akreditasi')->update([
            'status_akreditasi' => 'Terakreditasi Unggul',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prodi', function (Blueprint $table) {
            $table->dropForeign(['id_upps']);
            $table->dropColumn([
                'jenjang',
                'sk_pendirian',
                'tanggal_sk_pendirian',
                'status_akreditasi',
                'masa_berlaku_akreditasi',
                'id_upps',
            ]);
        });
    }
};
