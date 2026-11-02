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
                'jenjang' => 'D4',
                'sk_pendirian' => 'SK/POLINES/TRK/001',
                'tanggal_sk_pendirian' => '2020-01-15',
                'status_akreditasi' => 'Terakreditasi Unggul',
                'masa_berlaku_akreditasi' => '2028-12-31',
            ]
        );
    }
}
