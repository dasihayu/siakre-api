<?php

namespace Database\Seeders;

use App\Models\Prodi;
use App\Models\Upps;
use Illuminate\Database\Seeder;

class ProdiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $upps = Upps::where('kode_upps', 'UPPS01')->first() ?? Upps::first();

        if (! $upps) {
            return;
        }

        Prodi::updateOrCreate(
            ['kode_prodi' => 'PR01'],
            [
                'kode_prodi' => 'PR01',
                'nama_prodi' => 'Teknologi Rekayasa Komputer',
                'akreditasi' => 'Unggul',
                'id_upps' => $upps->id,
            ]
        );
    }
}
