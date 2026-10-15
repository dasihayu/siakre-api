<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Mahasiswa;

class MahasiswaSeeder extends Seeder
{
    public function run(): void
    {
        Mahasiswa::create([
            'nim'               => 33424101, // Int
            'nama_mahasiswa'    => 'Andrian Amirudin',
            'tahun_masuk'       => 2024,
            'jenis_pendaftaran' => 'Reguler',
            'tanggal_kelulusan' => '2028-09-13',
            'masa_studi_bulan'  => 48,
        ]);
    }
}