<?php

namespace Database\Seeders;

use App\Models\Cpl;
use App\Models\MataKuliah;
use Illuminate\Database\Seeder;

class MataKuliahCplSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mappings = [
            // Semester 1
            '432-191-101' => ['CPL-03', 'CPL-05'],
            '432-191-102' => ['CPL-03', 'CPL-05', 'CPL-06'],
            '432-191-103' => ['CPL-03', 'CPL-07'],
            '432-191-104' => ['CPL-03', 'CPL-07'],
            '432-191-105' => ['CPL-03', 'CPL-07'],
            '432-191-106' => ['CPL-03', 'CPL-05'],
            '432-191-107' => ['CPL-05', 'CPL-06'],

            // Semester 2
            '432-191-201' => ['CPL-03', 'CPL-05'],
            '432-191-202' => ['CPL-03', 'CPL-06'],
            '432-191-203' => ['CPL-03', 'CPL-08'],
            '432-191-204' => ['CPL-04', 'CPL-06'],
            '432-191-205' => ['CPL-04', 'CPL-06', 'CPL-08'],
            '432-191-206' => ['CPL-01', 'CPL-02'],
            '432-191-207' => ['CPL-02', 'CPL-05'],
            '432-191-208' => ['CPL-03', 'CPL-07'],
            '432-191-209' => ['CPL-04', 'CPL-06'],

            // Semester 3
            '432-191-301' => ['CPL-03', 'CPL-08'],
            '432-191-302' => ['CPL-03', 'CPL-07'],
            '432-191-303' => ['CPL-04', 'CPL-06'],
            '432-191-304' => ['CPL-03', 'CPL-05'],
            '432-191-305' => ['CPL-04', 'CPL-06'],
            '432-191-306' => ['CPL-03', 'CPL-07'],
            '432-191-307' => ['CPL-03', 'CPL-07'],

            // Semester 4
            '432-191-401' => ['CPL-04', 'CPL-08'],
            '432-191-402' => ['CPL-04', 'CPL-07'],
            '432-191-403' => ['CPL-01', 'CPL-02'],
            '432-191-404' => ['CPL-05', 'CPL-06'],
            '432-191-405' => ['CPL-04', 'CPL-06', 'CPL-07'],
            '432-191-406' => ['CPL-04', 'CPL-06'],
            '432-191-407' => ['CPL-03', 'CPL-08'],

            // Semester 5
            '432-191-501' => ['CPL-02', 'CPL-05'],
            '432-191-502' => ['CPL-04', 'CPL-05', 'CPL-06'],
            '432-191-503' => ['CPL-05', 'CPL-06'],
            '432-191-504' => ['CPL-04', 'CPL-07'],
            '432-191-505' => ['CPL-04', 'CPL-08'],
            '432-191-506' => ['CPL-04', 'CPL-07'],
            '432-191-507' => ['CPL-01', 'CPL-02'],

            // Semester 6
            '432-191-601' => ['CPL-04', 'CPL-06'],
            '432-191-602' => ['CPL-02', 'CPL-05'],
            '432-191-603' => ['CPL-03', 'CPL-07', 'CPL-08'],
            '432-191-604' => ['CPL-04', 'CPL-08'],
            '432-191-605' => ['CPL-03', 'CPL-07'],
            '432-191-606' => ['CPL-02', 'CPL-04', 'CPL-05'],
            '432-191-607' => ['CPL-04', 'CPL-05'],
            '432-191-608' => ['CPL-02', 'CPL-04', 'CPL-05'],

            // Semester 7
            '432-191-701' => ['CPL-04', 'CPL-07'],
            '432-191-702' => ['CPL-01', 'CPL-02'],
            '432-191-703' => ['CPL-04', 'CPL-06'],
            '432-191-704' => ['CPL-04', 'CPL-07', 'CPL-08'],
            '432-191-705' => ['CPL-03', 'CPL-07'],
            '432-191-706' => ['CPL-01', 'CPL-02', 'CPL-03', 'CPL-04', 'CPL-05', 'CPL-06', 'CPL-07', 'CPL-08'],

            // Semester 8
            '432-191-801' => ['CPL-01', 'CPL-02', 'CPL-04', 'CPL-05', 'CPL-06', 'CPL-07'],
        ];

        $cplMap = Cpl::pluck('id', 'kode_cpl');

        foreach ($mappings as $kodeMk => $kodeCpls) {
            $matkul = MataKuliah::where('kode_mata_kuliah', $kodeMk)->first();
            if (! $matkul) {
                continue;
            }

            $cplIds = collect($kodeCpls)
                ->map(fn (string $kode) => $cplMap->get($kode))
                ->filter()
                ->values()
                ->all();

            if (! empty($cplIds)) {
                $matkul->cpl()->syncWithoutDetaching($cplIds);
            }
        }
    }
}
