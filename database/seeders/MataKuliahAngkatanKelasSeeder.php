<?php

namespace Database\Seeders;

use App\Models\AngkatanKelas;
use App\Models\EnrollmentMataKuliah;
use App\Models\MataKuliahAngkatanKelas;
use App\Models\TahunAkademik;
use Illuminate\Database\Seeder;

class MataKuliahAngkatanKelasSeeder extends Seeder
{
    /**
     * Batas semester kurikulum maksimal yang sedang/telah ditempuh tiap angkatan aktif
     * pada posisi saat ini (Tahun Akademik aktif: 2026/2027 Ganjil).
     *
     * @return array<int, int>
     */
    public static function getMaxEnrolledSemesterByAngkatan(): array
    {
        return [
            2026 => 1, // Kelas TI-1A..1C: baru menempuh Semester 1 (belum ada nilai)
            2025 => 3, // Kelas TI-2A..2C: sudah ada nilai Sem 1-2, sedang menempuh Sem 3
            2024 => 5, // Kelas TI-3A..3C: sudah ada nilai Sem 1-4, sedang menempuh Sem 5
            2023 => 7, // Kelas TI-4A..4C: sudah ada nilai Sem 1-6, sedang menempuh Sem 7
        ];
    }

    /**
     * Run the database seeds.
     *
     * Menghubungkan penawaran mata kuliah tiap angkatan ke kelas aktifnya saat ini
     * (TI-1A..1C untuk 2026, TI-2A..2C untuk 2025, TI-3A..3C untuk 2024, TI-4A..4C untuk 2023)
     * sehingga pada Detail Mata Kuliah:
     * - Kelas yang tampil adalah kelas aktif (TI-1A..TI-4C tanpa duplikasi nama kelas).
     * - TI-1A..1C belum memiliki nilai (karena masih Tingkat 1 Semester 1).
     * - TI-2A..2C memiliki nilai pada MK Semester 1 & 2.
     * - TI-3A..3C memiliki nilai pada MK Semester 1 s/d 4.
     * - TI-4A..4C memiliki nilai pada MK Semester 1 s/d 6.
     */
    public function run(): void
    {
        $taActive = TahunAkademik::where('is_active', true)->first()
            ?? TahunAkademik::where('tahun', '2026/2027')->where('semester', 'Ganjil')->first()
            ?? TahunAkademik::latest('id')->first();

        if (! $taActive) {
            return;
        }

        $allEnrollments = EnrollmentMataKuliah::with(['mataKuliah', 'tahunAkademik'])->get();
        $activeAngkatanKelas = AngkatanKelas::with(['angkatan', 'kelas'])
            ->where('tahun_akademik_id', $taActive->id)
            ->get();

        if ($allEnrollments->isEmpty() || $activeAngkatanKelas->isEmpty()) {
            return;
        }

        $allTa = TahunAkademik::all();
        $taMap = $allTa->keyBy(fn (TahunAkademik $ta) => "{$ta->tahun}|{$ta->semester}");
        $enrollmentMap = $allEnrollments->keyBy(fn (EnrollmentMataKuliah $e) => "{$e->mata_kuliah_id}|{$e->tahun_akademik_id}");
        $akByAngkatan = $activeAngkatanKelas->groupBy(fn (AngkatanKelas $ak) => (int) ($ak->angkatan?->angkatan));

        $maxSemByAngkatan = self::getMaxEnrolledSemesterByAngkatan();
        $allMatkul = $allEnrollments->pluck('mataKuliah')->filter()->unique('id');

        MataKuliahAngkatanKelas::query()->delete();

        $now = now();
        $records = [];

        foreach ($allMatkul as $mk) {
            $sem = EnrollmentMataKuliahSeeder::extractSemesterFromKode($mk->kode_mata_kuliah);

            foreach ($maxSemByAngkatan as $angkatan => $maxSem) {
                if ($sem > $maxSem) {
                    continue;
                }

                $taInfo = EnrollmentMataKuliahSeeder::resolveTahunAkademikForCohort($angkatan, $sem);
                $ta = $taMap->get("{$taInfo['tahun']}|{$taInfo['semester']}");
                if (! $ta) {
                    continue;
                }

                $enrollment = $enrollmentMap->get("{$mk->id}|{$ta->id}");
                if (! $enrollment) {
                    continue;
                }

                $cohortActiveClasses = $akByAngkatan->get($angkatan, collect());
                foreach ($cohortActiveClasses as $ak) {
                    $records[] = [
                        'enrollment_mata_kuliah_id' => $enrollment->id,
                        'angkatan_kelas_id' => $ak->id,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
        }

        foreach (array_chunk($records, 500) as $chunk) {
            MataKuliahAngkatanKelas::insert($chunk);
        }
    }
}
