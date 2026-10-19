<?php

namespace Database\Seeders;

use App\Models\PerguruanTinggi;
use App\Models\Prodi;
use App\Models\Upps;
use Illuminate\Database\Seeder;

class UppsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pt = PerguruanTinggi::where('kode_pt', 'PT001')->first() ?? PerguruanTinggi::first();
        $prodi = Prodi::where('kode_prodi', 'PR01')->first() ?? Prodi::first();

        if (! $pt || ! $prodi) {
            return;
        }

        $uppsList = [
            [
                'id_pt' => $pt->id,
                'kode_upps' => 'UPPS01',
                'nama_upps' => 'Jurusan Teknik Elektro',
                'pimpinan_upps' => 'Dr. Amin Suharjono, S.T., M.T.',
                'prodi_id' => $prodi->id,
                'jenis' => 'Jurusan',
            ],
            [
                'id_pt' => $pt->id,
                'kode_upps' => 'UPPS02',
                'nama_upps' => 'Jurusan Teknik Mesin',
                'pimpinan_upps' => 'Hartono, S.T., M.T.',
                'prodi_id' => $prodi->id,
                'jenis' => 'Jurusan',
            ],
            [
                'id_pt' => $pt->id,
                'kode_upps' => 'UPPS03',
                'nama_upps' => 'Jurusan Teknik Sipil',
                'pimpinan_upps' => 'Dwi Yanuar, S.T., M.T.',
                'prodi_id' => $prodi->id,
                'jenis' => 'Jurusan',
            ],
        ];

        foreach ($uppsList as $item) {
            Upps::firstOrCreate(
                ['kode_upps' => $item['kode_upps']],
                $item
            );
        }
    }
}
