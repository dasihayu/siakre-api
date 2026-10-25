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
     * Mapping antara nama kelas dan tahun angkatan.
     *
     * @return array<string, int>
     */
    public static function getKelasAngkatanMap(): array
    {
        return [
            'TI-1A' => 2026,
            'TI-1B' => 2026,
            'TI-1C' => 2026,
            'TI-2A' => 2025,
            'TI-2B' => 2025,
            'TI-2C' => 2025,
            'TI-3A' => 2024,
            'TI-3B' => 2024,
            'TI-3C' => 2024,
            'TI-4A' => 2023,
            'TI-4B' => 2023,
            'TI-4C' => 2023,
        ];
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $prodiTrk = Prodi::where('kode_prodi', 'PR01')->first() ?? Prodi::first();
        $taActive = TahunAkademik::where('tahun', '2026/2027')->where('semester', 'Ganjil')->first()
            ?? TahunAkademik::where('is_active', true)->first()
            ?? TahunAkademik::first();

        if (! $prodiTrk || ! $taActive) {
            return;
        }

        $kelasMap = Kelas::pluck('id', 'kelas');
        $angkatanMap = Angkatan::pluck('id', 'angkatan');

        foreach (self::getKelasAngkatanMap() as $namaKelas => $tahunAngkatan) {
            $kelasId = $kelasMap->get($namaKelas);
            $angkatanId = $angkatanMap->get($tahunAngkatan);

            if ($kelasId && $angkatanId) {
                AngkatanKelas::updateOrCreate(
                    [
                        'kelas_id' => $kelasId,
                        'angkatan_id' => $angkatanId,
                        'prodi_id' => $prodiTrk->id,
                    ],
                    [
                        'kelas_id' => $kelasId,
                        'angkatan_id' => $angkatanId,
                        'prodi_id' => $prodiTrk->id,
                        'tahun_akademik_id' => $taActive->id,
                    ]
                );
            }
        }
    }
}
