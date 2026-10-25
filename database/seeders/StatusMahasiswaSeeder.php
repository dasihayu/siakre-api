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
        $enrollments = EnrollmentKelas::with([
            'mahasiswa',
            'angkatanKelas.tahunAkademik',
        ])->get();

        if ($enrollments->isEmpty()) {
            return;
        }

        StatusMahasiswa::query()->delete();

        $now = now();
        $records = [];

        foreach ($enrollments as $enrollment) {
            $mhs = $enrollment->mahasiswa;
            $ta = $enrollment->angkatanKelas?->tahunAkademik;
            $startYear = (int) substr((string) ($ta?->tahun ?? '2026/2027'), 0, 4);

            // Pada saat menempuh semester di kelasnya, mahasiswa berstatus Aktif;
            // jika sudah lulus pada periode terakhir (Tingkat 4 Genap) maka Lulus.
            $isGraduatedInTa = $mhs?->tanggal_kelulusan
                && $startYear >= ((int) $mhs->tahun_masuk + 3)
                && ($ta?->semester === 'Genap');

            $records[] = [
                'id_enrollment_mahasiswa_angkatan_kelas' => $enrollment->id,
                'status' => $isGraduatedInTa ? 'Lulus' : 'Aktif',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach (array_chunk($records, 500) as $chunk) {
            StatusMahasiswa::insert($chunk);
        }
    }
}
