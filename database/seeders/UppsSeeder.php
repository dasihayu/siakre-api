<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Upps;

class UppsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Upps::create([
            'id_pt'         => 1,
            'kode_upps'     => 'UPPS01',
            'nama_upps'     => 'Jurusan Teknik Elektro',
            'pimpinan_upps' => 'Joko Susilo',
            'prodi_id'      => 1,
            'jenis'         => 'Jurusan'
        ]);
    }
}
