<?php

namespace Database\Seeders;

use App\Models\Angkatan;
use App\Models\AngkatanKelas;
use App\Models\Kelas;
use App\Models\Prodi;
use App\Models\TahunAkademik;
use Illuminate\Database\Seeder;

class AngkatanKelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $prodiTrk = Prodi::where('kode_prodi', 'PR01')->first() ?? Prodi::first();
        $prodiTi = Prodi::where('kode_prodi', 'PR02')->first() ?? $prodiTrk;
        $ta2024 = TahunAkademik::where('tahun', 2024)->first() ?? TahunAkademik::first();

        if (! $prodiTrk || ! $ta2024) {
            return;
        }

        $combinations = [
            ['kelas' => 'TRK-1A', 'angkatan' => 2024, 'prodi' => $prodiTrk->id],
            ['kelas' => 'TRK-1B', 'angkatan' => 2024, 'prodi' => $prodiTrk->id],
            ['kelas' => 'TRK-2A', 'angkatan' => 2023, 'prodi' => $prodiTrk->id],
            ['kelas' => 'TRK-2B', 'angkatan' => 2023, 'prodi' => $prodiTrk->id],
            ['kelas' => 'TRK-3A', 'angkatan' => 2022, 'prodi' => $prodiTrk->id],
            ['kelas' => 'TRK-4A', 'angkatan' => 2021, 'prodi' => $prodiTrk->id],
            ['kelas' => 'TI-1A', 'angkatan' => 2024, 'prodi' => $prodiTi->id],
            ['kelas' => 'TI-2A', 'angkatan' => 2023, 'prodi' => $prodiTi->id],
        ];

        foreach ($combinations as $combo) {
            $kelas = Kelas::where('kelas', $combo['kelas'])->first();
            $angkatan = Angkatan::where('angkatan', $combo['angkatan'])->first();

            if ($kelas && $angkatan) {
                AngkatanKelas::firstOrCreate(
                    [
                        'kelas_id' => $kelas->id,
                        'angkatan_id' => $angkatan->id,
                        'prodi_id' => $combo['prodi'],
                        'tahun_akademik_id' => $ta2024->id,
                    ],
                    [
                        'kelas_id' => $kelas->id,
                        'angkatan_id' => $angkatan->id,
                        'prodi_id' => $combo['prodi'],
                        'tahun_akademik_id' => $ta2024->id,
                    ]
                );
            }
        }
    }
}
