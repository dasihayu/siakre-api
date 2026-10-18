<?php

namespace Database\Seeders;

use App\Models\EvidencePrestasiMahasiswa;
use Illuminate\Database\Seeder;

class EvidencePrestasiMahasiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        EvidencePrestasiMahasiswa::create([
            'id_prestasi_mahasiswa' => 1,
            'evidence' => 'Juara 1 OSN',
        ]);
    }
}
