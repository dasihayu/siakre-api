<?php

namespace Database\Seeders;

use App\Models\EnrollmentKelas;
use App\Models\Mahasiswa;
use App\Models\MataKuliahAngkatanKelas;
use App\Models\NilaiMahasiswa;
use Illuminate\Database\Seeder;

class NilaiMahasiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courses = MataKuliahAngkatanKelas::all();
        $allMahasiswa = Mahasiswa::all();

        if ($courses->isEmpty() || $allMahasiswa->isEmpty()) {
            return;
        }

        foreach ($courses as $course) {
            $studentIds = EnrollmentKelas::where('angkatan_kelas_id', $course->angkatan_kelas_id)
                ->pluck('id_mahasiswa');

            if ($studentIds->isEmpty()) {
                $studentIds = $allMahasiswa->take(3)->pluck('id');
            }

            foreach ($studentIds as $studentId) {
                $nilaiAngka = fake()->randomFloat(2, 65, 95);
                $calc = NilaiMahasiswa::hitungNilaiHurufDanBobot($nilaiAngka);

                NilaiMahasiswa::firstOrCreate(
                    [
                        'mata_kuliah_angkatan_kelas_id' => $course->id,
                        'mahasiswa_id' => $studentId,
                    ],
                    [
                        'semester' => 1,
                        'nilai_angka' => $nilaiAngka,
                        'nilai_huruf' => $calc['nilai_huruf'],
                        'bobot' => $calc['bobot'],
                        'link_nilai' => "https://docs.google.com/spreadsheets/d/e/2PACX-1vNilaiMahasiswa_{$course->id}_{$studentId}/pubhtml",
                        'link_evidence' => "https://drive.google.com/drive/folders/EvidenceNilai_{$course->id}_{$studentId}",
                    ]
                );
            }
        }
    }
}
