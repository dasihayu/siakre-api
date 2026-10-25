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

        Upps::updateOrCreate(
            ['kode_upps' => 'UPPS01'],
            [
                'id_pt' => $pt->id,
                'kode_upps' => 'UPPS01',
                'nama_upps' => 'UPPS TRK',
                'pimpinan_upps' => 'Dr. Amin Suharjono, S.T., M.T.',
                'prodi_id' => $prodi->id,
                'jenis' => 'Jurusan',
            ]
        );
    }
}
