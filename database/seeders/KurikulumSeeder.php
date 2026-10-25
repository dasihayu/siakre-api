<?php

namespace Database\Seeders;

use App\Models\Kurikulum;
use App\Models\Prodi;
use Illuminate\Database\Seeder;

class KurikulumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $prodiTrk = Prodi::where('kode_prodi', 'PR01')->first() ?? Prodi::first();

        if (! $prodiTrk) {
            return;
        }

        Kurikulum::updateOrCreate(
            ['nama_kurikulum' => 'Kurikulum Sarjana Terapan Teknologi Rekayasa Komputer'],
            [
                'id_prodi' => $prodiTrk->id,
                'nama_kurikulum' => 'Kurikulum Sarjana Terapan Teknologi Rekayasa Komputer',
                'berlaku_sampai' => 2028,
                'sk_kurikulum' => 'SK/POLINES/433-RK/2022',
            ]
        );
    }
}
