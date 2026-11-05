<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('angkatan', function (Blueprint $table) {
            $table->unsignedInteger('jumlah_pendaftar')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('angkatan', function (Blueprint $table) {
            $table->dropColumn('jumlah_pendaftar');
        });
    }
};
