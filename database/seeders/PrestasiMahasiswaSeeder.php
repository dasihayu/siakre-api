<?php

namespace Database\Seeders;

use App\Models\PrestasiMahasiswa;
use Illuminate\Database\Seeder;

class PrestasiMahasiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PrestasiMahasiswa::create([
            'id_mahasiswa' => 1,
            'nama_aktivitas' => 'Juara 1 OSN',
            'tahun' => 2024,
        ]);
    }
}
