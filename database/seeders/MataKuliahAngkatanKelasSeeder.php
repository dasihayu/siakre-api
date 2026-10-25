<?php

namespace Database\Seeders;

use App\Models\AngkatanKelas;
use App\Models\EnrollmentMataKuliah;
use App\Models\MataKuliahAngkatanKelas;
use Illuminate\Database\Seeder;

class MataKuliahAngkatanKelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Menghubungkan setiap penawaran mata kuliah (EnrollmentMataKuliah pada suatu Tahun Akademik)
     * ke 3 kelas paralel (A, B, C) sesuai tingkat mata kuliah tersebut pada Tahun Akademik yang sama.
     */
    public function run(): void
    {
        $allEnrollments = EnrollmentMataKuliah::with(['mataKuliah'])->get();
        $allAngkatanKelas = AngkatanKelas::with(['kelas'])->get();

        if ($allEnrollments->isEmpty() || $allAngkatanKelas->isEmpty()) {
            return;
        }

        $tingkatMap = AngkatanKelasSeeder::getKelasTingkatMap();

        // Group AngkatanKelas by "{tahun_akademik_id}|{tingkat}"
        $akByTaAndTingkat = $allAngkatanKelas->groupBy(function (AngkatanKelas $ak) use ($tingkatMap) {
            $namaKelas = (string) ($ak->kelas?->kelas ?? '');
            $tingkat = $tingkatMap[$namaKelas] ?? 1;

            return "{$ak->tahun_akademik_id}|{$tingkat}";
        });

        MataKuliahAngkatanKelas::query()->delete();

        $now = now();
        $records = [];

        foreach ($allEnrollments as $enrollment) {
            $mk = $enrollment->mataKuliah;
            if (! $mk) {
                continue;
            }

            $sem = EnrollmentMataKuliahSeeder::extractSemesterFromKode($mk->kode_mata_kuliah);
            $tingkat = EnrollmentMataKuliahSeeder::extractTingkatFromSemester($sem);

            $matchingAks = $akByTaAndTingkat->get("{$enrollment->tahun_akademik_id}|{$tingkat}", collect());

            foreach ($matchingAks as $ak) {
                $records[] = [
                    'enrollment_mata_kuliah_id' => $enrollment->id,
                    'angkatan_kelas_id' => $ak->id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        foreach (array_chunk($records, 500) as $chunk) {
            MataKuliahAngkatanKelas::insert($chunk);
        }
    }
}
