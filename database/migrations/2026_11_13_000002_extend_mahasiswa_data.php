<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('data_mahasiswa', function (Blueprint $table) {
            $table->foreignId('id_prodi')->nullable()->after('id')->constrained('prodi')->nullOnDelete();
            $table->string('kewarganegaraan', 3)->nullable();
            $table->string('negara')->nullable();
            $table->string('asal_sekolah_pt')->nullable();
            $table->foreignId('id_status_terkini')
                ->nullable()
                ->constrained('status_mahasiswa')
                ->nullOnDelete();
            $table->integer('masa_studi_bulan')->nullable()->default(null)->change();
        });

        DB::table('data_mahasiswa')
            ->whereNotNull('tanggal_kelulusan')
            ->orderBy('id')
            ->chunkById(500, function ($mahasiswaRows): void {
                foreach ($mahasiswaRows as $mahasiswa) {
                    $graduation = new DateTimeImmutable($mahasiswa->tanggal_kelulusan);
                    $months = (($graduation->format('Y') - (int) $mahasiswa->tahun_masuk) * 12)
                        + (int) $graduation->format('n') - 1;

                    DB::table('data_mahasiswa')
                        ->where('id', $mahasiswa->id)
                        ->update(['masa_studi_bulan' => max(0, $months)]);
                }
            });

        DB::table('data_mahasiswa')
            ->whereNull('tanggal_kelulusan')
            ->update(['masa_studi_bulan' => null]);
    }

    public function down(): void
    {
        DB::table('data_mahasiswa')
            ->whereNull('masa_studi_bulan')
            ->update(['masa_studi_bulan' => 48]);

        Schema::table('data_mahasiswa', function (Blueprint $table) {
            $table->dropForeign(['id_prodi']);
            $table->dropForeign(['id_status_terkini']);
            $table->dropColumn(['id_prodi', 'kewarganegaraan', 'negara', 'asal_sekolah_pt', 'id_status_terkini']);
            $table->integer('masa_studi_bulan')->default(48)->nullable(false)->change();
        });
    }
};
