<?php

namespace Database\Seeders;

use App\Models\EnrollmentKelas;
use App\Models\MataKuliahAngkatanKelas;
use App\Models\NilaiMahasiswa;
use Illuminate\Database\Seeder;

class NilaiMahasiswaSeeder extends Seeder
{
    /**
     * Data nilai riil terarah berdasarkan profil mahasiswa TRK Polines.
     *
     * @var array<string, array<int, float>>
     */
    protected array $studentScores = [
        '33424101' => [86.50, 88.00, 82.50, 79.00, 91.00, 85.00, 84.00, 87.50], // Andrian Amirudin
        '43325006' => [92.50, 95.00, 88.50, 90.00, 87.50, 94.00, 89.00, 93.00], // Dasi Hayu Permana
        '43325002' => [78.00, 81.50, 75.00, 77.50, 83.00, 74.00, 80.00, 79.50], // Andrian Amirudin Rahmanto
        '43325003' => [89.00, 91.50, 87.00, 85.50, 93.00, 88.00, 86.50, 90.50], // Annisa Nur Farikha
        '43325004' => [74.00, 76.50, 72.00, 79.00, 81.00, 73.50, 75.00, 77.00], // Choirul Raissa Pasha
        '43325019' => [85.00, 87.50, 80.00, 84.50, 88.00, 82.00, 86.00, 89.00], // Pranaja Luthfi Belian
        '43325001' => [83.50, 86.00, 84.00, 88.50, 82.00, 85.50, 87.00, 83.00], // Aisa Reta Lestari
        '43325007' => [71.00, 73.50, 68.00, 74.50, 62.00, 70.00, 72.50, 66.50], // Dimas Bayu Pratama
        '43325008' => [76.00, 78.50, 73.00, 58.00, 80.00, 75.50, 77.00, 74.00], // Dzidan Adrian Firmansyah
        '43323010' => [84.00, 87.00, 85.50, 82.00, 89.50, 86.00, 88.00, 91.00, 83.50, 85.00], // Rizky Fauzan Hidayat
        '43323015' => [91.00, 93.50, 89.00, 92.00, 90.50, 94.00, 88.50, 95.00, 89.00, 92.50], // Nabila Putri Cahyani
        '43322005' => [85.00, 88.00, 86.50, 83.00, 89.00, 87.50, 90.00, 92.00, 88.50, 91.50, 89.00, 94.50], // Bagas Wicaksono
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $enrollments = EnrollmentKelas::with('mahasiswa')->get();

        if ($enrollments->isEmpty()) {
            $fallback = [
                [
                    'enrollment_mahasiswa_angkatan_kelas_id' => 1,
                    'mata_kuliah_angkatan_kelas_id' => 1,
                    'nilai_angka' => 85.50,
                    'nilai_huruf' => 'A',
                    'bobot_nilai' => 4.00,
                    'status_kelulusan' => 'Lulus',
                ],
                [
                    'enrollment_mahasiswa_angkatan_kelas_id' => 2,
                    'mata_kuliah_angkatan_kelas_id' => 1,
                    'nilai_angka' => 75.00,
                    'nilai_huruf' => 'B',
                    'bobot_nilai' => 3.00,
                    'status_kelulusan' => 'Lulus',
                ],
                [
                    'enrollment_mahasiswa_angkatan_kelas_id' => 3,
                    'mata_kuliah_angkatan_kelas_id' => 2,
                    'nilai_angka' => 45.00,
                    'nilai_huruf' => 'E',
                    'bobot_nilai' => 0.00,
                    'status_kelulusan' => 'Tidak Lulus',
                ],
            ];

            foreach ($fallback as $item) {
                NilaiMahasiswa::firstOrCreate(
                    [
                        'enrollment_mahasiswa_angkatan_kelas_id' => $item['enrollment_mahasiswa_angkatan_kelas_id'],
                        'mata_kuliah_angkatan_kelas_id' => $item['mata_kuliah_angkatan_kelas_id'],
                    ],
                    $item
                );
            }

            return;
        }

        foreach ($enrollments as $enrollment) {
            $courses = MataKuliahAngkatanKelas::where('angkatan_kelas_id', $enrollment->angkatan_kelas_id)
                ->orderBy('id')
                ->get();

            if ($courses->isEmpty()) {
                continue;
            }

            $nim = (string) ($enrollment->mahasiswa?->nim ?? '');
            $scores = $this->studentScores[$nim] ?? null;

            $courseLimit = $scores !== null ? count($scores) : min(8, $courses->count());

            foreach ($courses->take($courseLimit) as $index => $course) {
                $score = $scores[$index] ?? (75.0 + (($enrollment->id * 5 + $index * 4) % 21));
                $grade = $this->calculateGrade($score);

                NilaiMahasiswa::firstOrCreate(
                    [
                        'enrollment_mahasiswa_angkatan_kelas_id' => $enrollment->id,
                        'mata_kuliah_angkatan_kelas_id' => $course->id,
                    ],
                    [
                        'enrollment_mahasiswa_angkatan_kelas_id' => $enrollment->id,
                        'mata_kuliah_angkatan_kelas_id' => $course->id,
                        'nilai_angka' => $score,
                        'nilai_huruf' => $grade['nilai_huruf'],
                        'bobot_nilai' => $grade['bobot_nilai'],
                        'status_kelulusan' => $grade['status_kelulusan'],
                    ]
                );
            }
        }
    }

    /**
     * Konversi nilai angka ke indeks huruf, bobot, dan status kelulusan standar akademik.
     *
     * @return array{nilai_huruf: string, bobot_nilai: float, status_kelulusan: string}
     */
    protected function calculateGrade(float $angka): array
    {
        if ($angka >= 85.0) {
            return [
                'nilai_huruf' => 'A',
                'bobot_nilai' => 4.00,
                'status_kelulusan' => 'Lulus',
            ];
        }

        if ($angka >= 80.0) {
            return [
                'nilai_huruf' => 'AB',
                'bobot_nilai' => 3.50,
                'status_kelulusan' => 'Lulus',
            ];
        }

        if ($angka >= 70.0) {
            return [
                'nilai_huruf' => 'B',
                'bobot_nilai' => 3.00,
                'status_kelulusan' => 'Lulus',
            ];
        }

        if ($angka >= 65.0) {
            return [
                'nilai_huruf' => 'BC',
                'bobot_nilai' => 2.50,
                'status_kelulusan' => 'Lulus',
            ];
        }

        if ($angka >= 60.0) {
            return [
                'nilai_huruf' => 'C',
                'bobot_nilai' => 2.00,
                'status_kelulusan' => 'Lulus',
            ];
        }

        if ($angka >= 50.0) {
            return [
                'nilai_huruf' => 'D',
                'bobot_nilai' => 1.00,
                'status_kelulusan' => 'Tidak Lulus',
            ];
        }

        return [
            'nilai_huruf' => 'E',
            'bobot_nilai' => 0.00,
            'status_kelulusan' => 'Tidak Lulus',
        ];
    }
}
