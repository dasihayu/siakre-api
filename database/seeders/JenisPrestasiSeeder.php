<?php

namespace Database\Seeders;

use App\Models\JenisPrestasi;
use App\Models\PrestasiMahasiswa;
use Illuminate\Database\Seeder;

class JenisPrestasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $allPrestasi = PrestasiMahasiswa::all();

        foreach ($allPrestasi as $prestasi) {
            $isNonAkademik = str_contains(strtolower($prestasi->nama_aktivitas), 'english')
                || str_contains(strtolower($prestasi->nama_aktivitas), 'olahraga')
                || str_contains(strtolower($prestasi->nama_aktivitas), 'seni');

            $jenis = $isNonAkademik ? 'Non-Akademik' : 'Akademik';

            JenisPrestasi::firstOrCreate(
                [
                    'id_prestasi_mahasiswa' => $prestasi->id,
                    'jenis' => $jenis,
                ],
                [
                    'id_prestasi_mahasiswa' => $prestasi->id,
                    'jenis' => $jenis,
                ]
            );
        }
    }
}
