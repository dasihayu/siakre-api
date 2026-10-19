<?php

namespace Database\Seeders;

use App\Models\AngkatanKelas;
use App\Models\EnrollmentMataKuliah;
use App\Models\MataKuliahAngkatanKelas;
use Illuminate\Database\Seeder;

class MataKuliahAngkatanKelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $allEnrollments = EnrollmentMataKuliah::all();
        $allAngkatanKelas = AngkatanKelas::all();

        if ($allEnrollments->isEmpty() || $allAngkatanKelas->isEmpty()) {
            return;
        }

        foreach ($allEnrollments as $enrollment) {
            // Hubungkan setiap mata kuliah yang ditawarkan ke angkatan kelas yang sesuai
            foreach ($allAngkatanKelas as $ak) {
                MataKuliahAngkatanKelas::firstOrCreate(
                    [
                        'enrollment_mata_kuliah_id' => $enrollment->id,
                        'angkatan_kelas_id' => $ak->id,
                    ],
                    [
                        'enrollment_mata_kuliah_id' => $enrollment->id,
                        'angkatan_kelas_id' => $ak->id,
                    ]
                );
            }
        }
    }
}
