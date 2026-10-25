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
     * Run the database seeds.
     */
    public function run(): void
    {
        $allEnrollments = EnrollmentMataKuliah::with(['mataKuliah', 'tahunAkademik'])->get();
        $allAngkatanKelas = AngkatanKelas::with(['angkatan', 'kelas'])->get();
        $allTa = TahunAkademik::all();

        if ($allEnrollments->isEmpty() || $allAngkatanKelas->isEmpty()) {
            return;
        }

        $taMap = $allTa->keyBy(fn (TahunAkademik $ta) => "{$ta->tahun}|{$ta->semester}");
        $enrollmentMap = $allEnrollments->keyBy(fn (EnrollmentMataKuliah $e) => "{$e->mata_kuliah_id}|{$e->tahun_akademik_id}");
        $akByAngkatan = $allAngkatanKelas->groupBy(fn (AngkatanKelas $ak) => (int) ($ak->angkatan?->angkatan));

        $maxSemByAngkatan = EnrollmentMataKuliahSeeder::getMaxEnrolledSemesterByAngkatan();
        $allMatkul = $allEnrollments->pluck('mataKuliah')->filter()->unique('id');

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

                $cohortClasses = $akByAngkatan->get($angkatan, collect());
                foreach ($cohortClasses as $ak) {
                    MataKuliahAngkatanKelas::updateOrCreate(
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
}
