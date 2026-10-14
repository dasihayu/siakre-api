<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MataKuliah;
use App\Models\Cpl;

class MataKuliahCplSeeder extends Seeder
{
    public function run(): void
    {
        $matkul = MataKuliah::first();
        $cpl = Cpl::first();

        if ($matkul && $cpl) {
            // Menghubungkan Mata Kuliah dengan CPL via tabel pivot
            $matkul->cpl()->sync([$cpl->id]);
        }
    }
}