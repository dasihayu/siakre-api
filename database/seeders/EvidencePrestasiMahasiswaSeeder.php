<?php

namespace Database\Seeders;

use App\Models\EvidencePrestasiMahasiswa;
use App\Models\PrestasiMahasiswa;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class EvidencePrestasiMahasiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $allPrestasi = PrestasiMahasiswa::all();

        foreach ($allPrestasi as $prestasi) {
            $slug = Str::slug($prestasi->nama_aktivitas, '_');
            $fileName = "sertifikat_{$slug}_{$prestasi->tahun}.pdf";

            EvidencePrestasiMahasiswa::firstOrCreate(
                [
                    'id_prestasi_mahasiswa' => $prestasi->id,
                    'evidence' => $fileName,
                ],
                [
                    'id_prestasi_mahasiswa' => $prestasi->id,
                    'evidence' => $fileName,
                ]
            );
        }
    }
}
