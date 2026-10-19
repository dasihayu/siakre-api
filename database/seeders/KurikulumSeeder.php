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
        $prodiTi = Prodi::where('kode_prodi', 'PR02')->first() ?? $prodiTrk;

        if (! $prodiTrk) {
            return;
        }

        $kurikulums = [
            [
                'id_prodi' => $prodiTrk->id,
                'nama_kurikulum' => 'Kurikulum Merdeka TRK 2024',
                'berlaku_sampai' => 2028,
                'sk_kurikulum' => 'SK/2024/001/TRK',
            ],
            [
                'id_prodi' => $prodiTrk->id,
                'nama_kurikulum' => 'Kurikulum OBE TRK 2022',
                'berlaku_sampai' => 2026,
                'sk_kurikulum' => 'SK/2022/015/TRK',
            ],
            [
                'id_prodi' => $prodiTi->id,
                'nama_kurikulum' => 'Kurikulum D3 Teknik Informatika 2023',
                'berlaku_sampai' => 2027,
                'sk_kurikulum' => 'SK/2023/008/TI',
            ],
        ];

        foreach ($kurikulums as $item) {
            Kurikulum::firstOrCreate(
                ['nama_kurikulum' => $item['nama_kurikulum']],
                $item
            );
        }
    }
}
