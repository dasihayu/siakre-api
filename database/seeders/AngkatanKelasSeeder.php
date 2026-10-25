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
     * Mapping antara nama kelas dan tingkat (1..4).
     *
     * @return array<string, int>
     */
    public static function getKelasTingkatMap(): array
    {
        return [
            'TI-1A' => 1,
            'TI-1B' => 1,
            'TI-1C' => 1,
            'TI-2A' => 2,
            'TI-2B' => 2,
            'TI-2C' => 2,
            'TI-3A' => 3,
            'TI-3B' => 3,
            'TI-3C' => 3,
            'TI-4A' => 4,
            'TI-4B' => 4,
            'TI-4C' => 4,
        ];
    }

    /**
     * Run the database seeds.
     *
     * Setiap Tahun Akademik memiliki 12 kelas (TI-1A s/d TI-4C).
     * Pada Tahun Akademik X/(X+1):
     * - Tingkat 1 (TI-1A..1C) ditempati oleh Angkatan X
     * - Tingkat 2 (TI-2A..2C) ditempati oleh Angkatan X - 1
     * - Tingkat 3 (TI-3A..3C) ditempati oleh Angkatan X - 2
     * - Tingkat 4 (TI-4A..4C) ditempati oleh Angkatan X - 3
     */
    public function run(): void
    {
        $prodiTrk = Prodi::where('kode_prodi', 'PR01')->first() ?? Prodi::first();
        $allTa = TahunAkademik::all();

        if (! $prodiTrk || $allTa->isEmpty()) {
            return;
        }

        $kelasMap = Kelas::pluck('id', 'kelas');
        $angkatanMap = Angkatan::pluck('id', 'angkatan');

        foreach ($allTa as $ta) {
            $startYear = (int) substr((string) $ta->tahun, 0, 4);
            if ($startYear <= 0) {
                $startYear = 2026;
            }

            foreach (self::getKelasTingkatMap() as $namaKelas => $tingkat) {
                $tahunAngkatan = $startYear - ($tingkat - 1);
                $kelasId = $kelasMap->get($namaKelas);
                $angkatanId = $angkatanMap->get($tahunAngkatan);

                if ($kelasId && $angkatanId) {
                    AngkatanKelas::updateOrCreate(
                        [
                            'tahun_akademik_id' => $ta->id,
                            'kelas_id' => $kelasId,
                            'prodi_id' => $prodiTrk->id,
                        ],
                        [
                            'tahun_akademik_id' => $ta->id,
                            'kelas_id' => $kelasId,
                            'angkatan_id' => $angkatanId,
                            'prodi_id' => $prodiTrk->id,
                        ]
                    );
                }
            }
        }
    }
}
