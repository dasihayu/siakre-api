<?php

namespace Database\Seeders;

use App\Models\EnrollmentKelas;
use App\Models\StatusMahasiswa;
use Illuminate\Database\Seeder;

class StatusMahasiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $enrollments = EnrollmentKelas::with('mahasiswa')->get();

        foreach ($enrollments as $enrollment) {
            $status = $enrollment->mahasiswa?->tanggal_kelulusan ? 'Lulus' : 'Aktif';

            StatusMahasiswa::firstOrCreate(
                ['id_enrollment_mahasiswa_angkatan_kelas' => $enrollment->id],
                [
                    'id_enrollment_mahasiswa_angkatan_kelas' => $enrollment->id,
                    'status' => $status,
                ]
            );
        }
    }
}
