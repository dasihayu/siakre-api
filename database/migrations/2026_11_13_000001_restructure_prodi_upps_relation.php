<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prodi', function (Blueprint $table) {
            $table->foreignId('id_upps')->nullable()->after('id')->constrained('upps')->restrictOnDelete();
        });

        DB::table('upps')
            ->whereNotNull('prodi_id')
            ->orderBy('id')
            ->get(['id', 'prodi_id'])
            ->each(function (object $upps): void {
                DB::table('prodi')
                    ->where('id', $upps->prodi_id)
                    ->update(['id_upps' => $upps->id]);
            });

        Schema::table('upps', function (Blueprint $table) {
            $table->dropForeign(['prodi_id']);
            $table->dropColumn('prodi_id');
        });
    }

    public function down(): void
    {
        Schema::table('upps', function (Blueprint $table) {
            $table->foreignId('prodi_id')->nullable()->constrained('prodi')->restrictOnDelete();
        });

        DB::table('upps')->orderBy('id')->get(['id'])->each(function (object $upps): void {
            $prodiId = DB::table('prodi')
                ->where('id_upps', $upps->id)
                ->orderBy('id')
                ->value('id');

            DB::table('upps')->where('id', $upps->id)->update(['prodi_id' => $prodiId]);
        });

        Schema::table('prodi', function (Blueprint $table) {
            $table->dropForeign(['id_upps']);
            $table->dropColumn('id_upps');
        });
    }
};
