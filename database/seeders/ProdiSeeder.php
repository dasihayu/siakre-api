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
        $prodis = [
            [
                'kode_prodi' => 'PR01',
                'nama_prodi' => 'S.Tr. Teknologi Rekayasa Komputer',
                'akreditasi' => 'Unggul',
            ],
            [
                'kode_prodi' => 'PR02',
                'nama_prodi' => 'D3 Teknik Informatika',
                'akreditasi' => 'Unggul',
            ],
            [
                'kode_prodi' => 'PR03',
                'nama_prodi' => 'D3 Teknik Telekomunikasi',
                'akreditasi' => 'Baik Sekali',
            ],
            [
                'kode_prodi' => 'PR04',
                'nama_prodi' => 'D3 Teknik Listrik',
                'akreditasi' => 'Unggul',
            ],
            [
                'kode_prodi' => 'PR05',
                'nama_prodi' => 'D3 Teknik Elektronika',
                'akreditasi' => 'Unggul',
            ],
            [
                'kode_prodi' => 'PR06',
                'nama_prodi' => 'S.Tr. Rekayasa Perangkat Lunak Terapan',
                'akreditasi' => 'Baik Sekali',
            ],
        ];

        foreach ($prodis as $item) {
            Prodi::firstOrCreate(
                ['kode_prodi' => $item['kode_prodi']],
                $item
            );
        }
    }
}
