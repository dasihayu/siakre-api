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
        // 1. Update struktur tabel jenis_prestasi menjadi master murni
        Schema::table('jenis_prestasi', function (Blueprint $table) {
            if (Schema::hasColumn('jenis_prestasi', 'id_prestasi_mahasiswa')) {
                try {
                    $table->dropForeign(['id_prestasi_mahasiswa']);
                } catch (\Exception $e) {
                    // Abaikan jika constraint tidak ada
                }
                $table->dropColumn('id_prestasi_mahasiswa');
            }
            if (Schema::hasColumn('jenis_prestasi', 'jenis')) {
                $table->dropColumn('jenis');
            }
            
            // Tambahkan kolom master baru jika belum ada
            if (!Schema::hasColumn('jenis_prestasi', 'kode')) {
                $table->string('kode')->unique()->after('id');
            }
            if (!Schema::hasColumn('jenis_prestasi', 'nama')) {
                $table->string('nama')->after('kode');
            }
            if (!Schema::hasColumn('jenis_prestasi', 'kategori')) {
                $table->enum('kategori', ['Akademik', 'Non-Akademik'])->default('Akademik')->after('nama');
            }
            if (!Schema::hasColumn('jenis_prestasi', 'tingkat')) {
                $table->enum('tingkat', ['Internasional', 'Nasional', 'Regional', 'Lokal'])->default('Nasional')->after('kategori');
            }
            if (!Schema::hasColumn('jenis_prestasi', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('tingkat');
            }
        });

        // 2. Tambahkan foreign key id_jenis_prestasi pada tabel prestasi_mahasiswa
        Schema::table('prestasi_mahasiswa', function (Blueprint $table) {
            if (!Schema::hasColumn('prestasi_mahasiswa', 'id_jenis_prestasi')) {
                $table->foreignId('id_jenis_prestasi')
                    ->nullable()
                    ->after('id')
                    ->constrained('jenis_prestasi')
                    ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prestasi_mahasiswa', function (Blueprint $table) {
            if (Schema::hasColumn('prestasi_mahasiswa', 'id_jenis_prestasi')) {
                $table->dropForeign(['id_jenis_prestasi']);
                $table->dropColumn('id_jenis_prestasi');
            }
        });

        Schema::table('jenis_prestasi', function (Blueprint $table) {
            $table->dropColumn(['kode', 'nama', 'kategori', 'tingkat', 'is_active']);
        });
    }
};