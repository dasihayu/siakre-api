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
     * Run the database seeds.
     */
    public function run(): void
    {
        $ta2024 = TahunAkademik::where('tahun', 2024)->first() ?? TahunAkademik::first();
        $dosens = User::where('role', UserRole::DOSEN)->get();
        $defaultDosen = $dosens->first();
        $allMatkul = MataKuliah::all();

        if (! $ta2024 || $allMatkul->isEmpty()) {
            return;
        }

        foreach ($allMatkul as $index => $mk) {
            // Rotasi dosen pengampu
            $dosen = $dosens->isNotEmpty()
                ? $dosens[$index % $dosens->count()]
                : $defaultDosen;

            EnrollmentMataKuliah::firstOrCreate(
                [
                    'tahun_akademik_id' => $ta2024->id,
                    'mata_kuliah_id' => $mk->id,
                ],
                [
                    'tahun_akademik_id' => $ta2024->id,
                    'mata_kuliah_id' => $mk->id,
                    'dosen_id' => $dosen?->id,
                    'evidence' => 1,
                    'mata_kuliah_angkatan_kelas_id' => null,
                ]
            );
        }
    }
}
