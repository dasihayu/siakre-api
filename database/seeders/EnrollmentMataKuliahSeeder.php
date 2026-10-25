<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\EnrollmentMataKuliah;
use App\Models\MataKuliah;
use App\Models\TahunAkademik;
use App\Models\User;
use Illuminate\Database\Seeder;

class EnrollmentMataKuliahSeeder extends Seeder
{
    /**
     * Ekstrak nomor semester kurikulum (1..8) dari kode mata kuliah (misal '432-191-305' -> 3).
     */
    public static function extractSemesterFromKode(string $kode): int
    {
        if (preg_match('/-(\d)\d{2}$/', $kode, $m)) {
            return (int) $m[1];
        }

        return 1;
    }

    /**
     * Hitung tingkat kelas (1..4) dari nomor semester kurikulum (1..8).
     */
    public static function extractTingkatFromSemester(int $sem): int
    {
        return max(1, min(4, intdiv($sem + 1, 2)));
    }

    /**
     * Hitung tahun akademik (misal '2025/2026') dan semester ('Ganjil'/'Genap')
     * saat suatu angkatan menempuh semester kurikulum ke-$sem (1..8).
     *
     * @return array{tahun: string, semester: string}
     */
    public static function resolveTahunAkademikForCohort(int $angkatan, int $sem): array
    {
        $startYear = $angkatan + intdiv($sem - 1, 2);
        $nextYear = $startYear + 1;

        return [
            'tahun' => "{$startYear}/{$nextYear}",
            'semester' => ($sem % 2 === 1) ? 'Ganjil' : 'Genap',
        ];
    }

    /**
     * Run the database seeds.
     *
     * Setiap Tahun Akademik Ganjil menawarkan mata kuliah Semester 1, 3, 5, 7 (untuk Tingkat 1..4),
     * dan setiap Tahun Akademik Genap menawarkan mata kuliah Semester 2, 4, 6, 8 (untuk Tingkat 1..4).
     */
    public function run(): void
    {
        $dosens = User::where('role', UserRole::DOSEN)->get();
        $defaultDosen = $dosens->first();
        $allMatkul = MataKuliah::all();
        $allTa = TahunAkademik::all();

        if ($allMatkul->isEmpty() || $allTa->isEmpty()) {
            return;
        }

        foreach ($allMatkul as $index => $mk) {
            $sem = self::extractSemesterFromKode($mk->kode_mata_kuliah);
            $expectedJenisSemester = ($sem % 2 === 1) ? 'Ganjil' : 'Genap';
            $dosen = $dosens->isNotEmpty()
                ? $dosens[$index % $dosens->count()]
                : $defaultDosen;

            foreach ($allTa as $ta) {
                if ($ta->semester !== $expectedJenisSemester) {
                    continue;
                }

                EnrollmentMataKuliah::updateOrCreate(
                    [
                        'tahun_akademik_id' => $ta->id,
                        'mata_kuliah_id' => $mk->id,
                    ],
                    [
                        'tahun_akademik_id' => $ta->id,
                        'mata_kuliah_id' => $mk->id,
                        'dosen_id' => $dosen?->id,
                        'evidence' => 1,
                        'mata_kuliah_angkatan_kelas_id' => null,
                    ]
                );
            }
        }
    }
}
