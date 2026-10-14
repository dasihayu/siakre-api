<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Mahasiswa;

class MahasiswaSeeder extends Seeder
{
    public function run(): void
    {
        Mahasiswa::create([
            'nim'                    => '3.34.24.1.01',
            'nama_mahasiswa'         => 'Andrian Amirudin',
            'tahun_masuk'            => 2024,
            'jenis_pendaftaran'      => 'Reguler',
            'mahasiswa_internasional'=> false,
        ]);
    }
}