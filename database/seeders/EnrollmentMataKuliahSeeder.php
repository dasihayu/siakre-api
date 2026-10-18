<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EnrollmentMataKuliah;
use App\Models\TahunAkademik;
use App\Models\MataKuliah;

class EnrollmentMataKuliahSeeder extends Seeder
{
    public function run(): void
    {
        $ta = TahunAkademik::first();
        $mk = MataKuliah::first();

        if ($ta && $mk) {
            EnrollmentMataKuliah::create([
                'tahun_akademik_id' => $ta->id,
                'mata_kuliah_id'    => $mk->id,
                'dosen_id'          => null, // Opsional
                'evidence'          => null, // Opsional
                'mata_kuliah_angkatan_kelas_id' => null, // Opsional
            ]);
        }
    }
}