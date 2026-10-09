<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DataKelulusan;
use App\Models\Mahasiswa;

class DataKelulusanSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil data mahasiswa pertama untuk dihubungkan ke data kelulusan
        $mahasiswa = Mahasiswa::first();

        if ($mahasiswa) {
            DataKelulusan::create([
                'id_mahasiswa'      => $mahasiswa->id,
                'tanggal_kelulusan' => '2026-09-13',
                'ipk'               => 3.88,
                'masa_studi_bulan'  => 48,
            ]);
        }
    }
}