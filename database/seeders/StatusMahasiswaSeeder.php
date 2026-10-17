<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StatusMahasiswa;
use App\Models\EnrollmentKelas;

class StatusMahasiswaSeeder extends Seeder
{
    public function run(): void
    {
        $enrollment = EnrollmentKelas::first();

        if ($enrollment) {
            StatusMahasiswa::create([
                'id_enrollment_mahasiswa_angkatan_kelas' => $enrollment->id,
                'status'                                 => 'Aktif',
            ]);
        }
    }
}