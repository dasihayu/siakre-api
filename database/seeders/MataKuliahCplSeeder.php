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
            'IF101' => ['CPL-05', 'CPL-07'],
            'TRK101' => ['CPL-03', 'CPL-05'],
            'TRK102' => ['CPL-03', 'CPL-07'],
            'TRK103' => ['CPL-03', 'CPL-06'],
            'TRK104' => ['CPL-03', 'CPL-08'],
            'TRK201' => ['CPL-05', 'CPL-07'],
            'TRK202' => ['CPL-04', 'CPL-08'],
            'TRK203' => ['CPL-04', 'CPL-06'],
            'TRK204' => ['CPL-04', 'CPL-07'],
            'TRK301' => ['CPL-04', 'CPL-05', 'CPL-07'],
            'TRK302' => ['CPL-04', 'CPL-08'],
            'TRK401' => ['CPL-01', 'CPL-02', 'CPL-05', 'CPL-06', 'CPL-07', 'CPL-08'],
        ];

        foreach ($mappings as $kodeMk => $kodeCpls) {
            $matkul = MataKuliah::where('kode_mata_kuliah', $kodeMk)->first();
            if (! $matkul) {
                continue;
            }

            $cplIds = Cpl::whereIn('kode_cpl', $kodeCpls)->pluck('id')->toArray();
            if (! empty($cplIds)) {
                $matkul->cpl()->syncWithoutDetaching($cplIds);
            }
        }
    }
}
