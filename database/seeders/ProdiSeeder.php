<?php

namespace Database\Seeders;

use App\Models\Prodi;
use Illuminate\Database\Seeder;

class ProdiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Prodi::updateOrCreate(
            ['kode_prodi' => 'PR01'],
            [
                'kode_prodi' => 'PR01',
                'nama_prodi' => 'Teknologi Rekayasa Komputer',
                'akreditasi' => 'Unggul',
            ]
        );
    }
}
