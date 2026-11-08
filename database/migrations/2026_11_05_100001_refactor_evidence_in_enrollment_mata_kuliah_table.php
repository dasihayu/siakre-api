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
        // Bersihkan integer lama jika ada sebelum diubah ke format JSON
        if (Schema::hasColumn('enrollment_mata_kuliah', 'evidence')) {
            DB::table('enrollment_mata_kuliah')->whereNotNull('evidence')->update(['evidence' => null]);
        }

        Schema::table('enrollment_mata_kuliah', function (Blueprint $table) {
            $table->json('evidence')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('enrollment_mata_kuliah', 'evidence')) {
            DB::table('enrollment_mata_kuliah')->whereNotNull('evidence')->update(['evidence' => null]);
        }

        Schema::table('enrollment_mata_kuliah', function (Blueprint $table) {
            $table->integer('evidence')->nullable()->change();
        });
    }
};

