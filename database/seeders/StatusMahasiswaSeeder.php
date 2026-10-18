<?php

namespace Database\Seeders;

use App\Models\EnrollmentKelas;
use App\Models\StatusMahasiswa;
use Illuminate\Database\Seeder;

class StatusMahasiswaSeeder extends Seeder
{
    public function run(): void
    {
        $enrollment = EnrollmentKelas::first();

        if ($enrollment) {
            StatusMahasiswa::create([
                'id_enrollment_mahasiswa_angkatan_kelas' => $enrollment->id,
                'status' => 'Aktif',
            ]);
        }
    }
}
