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
        if (Schema::hasColumn('evidence_prestasi_mahasiswa', 'evidence')) {
            Schema::table('evidence_prestasi_mahasiswa', function (Blueprint $table) {
                $table->renameColumn('evidence', 'url');
            });
        }

        Schema::table('evidence_prestasi_mahasiswa', function (Blueprint $table) {
            $table->string('url', 2048)->change();
            $table->string('jenis_evidence')->default('Sertifikat')->after('id_prestasi_mahasiswa');
            $table->string('judul')->nullable()->after('jenis_evidence');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evidence_prestasi_mahasiswa', function (Blueprint $table) {
            $table->dropColumn(['jenis_evidence', 'judul']);
        });

        if (Schema::hasColumn('evidence_prestasi_mahasiswa', 'url')) {
            Schema::table('evidence_prestasi_mahasiswa', function (Blueprint $table) {
                $table->renameColumn('url', 'evidence');
            });
        }

        Schema::table('evidence_prestasi_mahasiswa', function (Blueprint $table) {
            $table->string('evidence', 255)->change();
        });
    }
};
