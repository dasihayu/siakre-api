<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('anggota_upps', function (Blueprint $table) {
            $table->foreignId('parent_id')->nullable()->constrained('anggota_upps')->nullOnDelete();
            $table->unsignedInteger('urutan_tampil')->default(0);
            $table->date('periode_mulai')->nullable();
            $table->date('periode_selesai')->nullable();
        });

        Schema::create('evidence_kegiatan_mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_kegiatan')
                ->constrained('kegiatan_mahasiswa')
                ->cascadeOnDelete();
            $table->string('url');
            $table->string('jenis_evidence');
            $table->string('judul');
            $table->timestamps();
        });

        DB::table('kegiatan_mahasiswa')
            ->where('status_validasi', 'Disetujui')
            ->update(['status_validasi' => 'Valid']);

        DB::table('kegiatan_mahasiswa')
            ->whereIn('tingkat', ['Fakultas', 'Jurusan', 'Polines'])
            ->update(['tingkat' => 'Lokal']);

        DB::table('kegiatan_mahasiswa')
            ->where('tingkat', 'Regional')
            ->update(['tingkat' => 'Provinsi']);
    }

    public function down(): void
    {
        Schema::dropIfExists('evidence_kegiatan_mahasiswa');

        Schema::table('anggota_upps', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['parent_id', 'urutan_tampil', 'periode_mulai', 'periode_selesai']);
        });
    }
};
