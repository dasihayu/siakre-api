<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MataKuliahAngkatanKelas;
use App\Models\EnrollmentMataKuliah;
use App\Models\AngkatanKelas;

class MataKuliahAngkatanKelasSeeder extends Seeder
{
    public function run(): void
    {
        $enrollmentMK = EnrollmentMataKuliah::first();
        $angkatanKelas = AngkatanKelas::first();

        if ($enrollmentMK && $angkatanKelas) {
            MataKuliahAngkatanKelas::create([
                'enrollment_mata_kuliah_id' => $enrollmentMK->id,
                'angkatan_kelas_id'         => $angkatanKelas->id,
            ]);
        }
    }
}