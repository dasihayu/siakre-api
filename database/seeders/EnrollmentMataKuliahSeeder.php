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
     * Batas semester kurikulum maksimal yang telah/sedang ditempuh tiap angkatan
     * pada posisi saat ini (Tahun Akademik aktif: 2026/2027 Ganjil).
     *
     * @return array<int, int>
     */
    public static function getMaxEnrolledSemesterByAngkatan(): array
    {
        return [
            2026 => 1, // Tingkat 1 Semester 1
            2025 => 3, // Tingkat 2 Semester 1 (Sem 3)
            2024 => 5, // Tingkat 3 Semester 1 (Sem 5)
            2023 => 8, // Tingkat 4 Semester 1 (Sem 7 & penawaran Sem 8)
        ];
    }

    /**
     * Run the database seeds.
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

        $taMap = $allTa->keyBy(fn (TahunAkademik $ta) => "{$ta->tahun}|{$ta->semester}");
        $maxSemByAngkatan = self::getMaxEnrolledSemesterByAngkatan();

        foreach ($allMatkul as $index => $mk) {
            $sem = self::extractSemesterFromKode($mk->kode_mata_kuliah);
            $dosen = $dosens->isNotEmpty()
                ? $dosens[$index % $dosens->count()]
                : $defaultDosen;

            foreach ($maxSemByAngkatan as $angkatan => $maxSem) {
                if ($sem > $maxSem) {
                    continue;
                }

                $taInfo = self::resolveTahunAkademikForCohort($angkatan, $sem);
                $ta = $taMap->get("{$taInfo['tahun']}|{$taInfo['semester']}");

                if (! $ta) {
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
