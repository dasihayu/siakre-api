<?php

namespace Database\Seeders;

use App\Models\AngkatanKelas;
use App\Models\EnrollmentKelas;
use App\Models\Mahasiswa;
use App\Models\Prodi;
use Illuminate\Database\Seeder;

class EnrollmentKelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $prodiTrk = Prodi::where('kode_prodi', 'PR01')->first() ?? Prodi::first();
        if (! $prodiTrk) {
            return;
        }

        $allMahasiswa = Mahasiswa::all();
        $angkatanKelasList = AngkatanKelas::with(['kelas', 'angkatan'])->get();

        if ($angkatanKelasList->isEmpty()) {
            return;
        }

        // Map nama kelas (misal 'TI-2A') -> AngkatanKelas model
        $akByNamaKelas = $angkatanKelasList->keyBy(fn ($ak) => $ak->kelas?->kelas);

        // Map kode tahun NIM ('26', '25', '24', '23') -> tingkat ('1', '2', '3', '4')
        $aaToTingkat = [
            '26' => 1,
            '25' => 2,
            '24' => 3,
            '23' => 4,
        ];

        $bToHuruf = [
            '0' => 'A',
            '1' => 'B',
            '2' => 'C',
        ];

        foreach ($allMahasiswa as $mhs) {
            $nimStr = (string) $mhs->nim;
            $targetKelasName = null;

            // Format NIM: 433 + aa (2 digit) + b (1 digit) + cc (2 digit)
            if (strlen($nimStr) === 8 && str_starts_with($nimStr, '433')) {
                $aa = substr($nimStr, 3, 2);
                $b = substr($nimStr, 5, 1);
                $tingkat = $aaToTingkat[$aa] ?? null;
                $huruf = $bToHuruf[$b] ?? null;

                if ($tingkat && $huruf) {
                    $targetKelasName = "TI-{$tingkat}{$huruf}";
                }
            }

            $matchedAk = ($targetKelasName && $akByNamaKelas->has($targetKelasName))
                ? $akByNamaKelas->get($targetKelasName)
                : ($angkatanKelasList->first(fn ($ak) => $ak->angkatan?->angkatan == $mhs->tahun_masuk) ?? $angkatanKelasList->first());

            EnrollmentKelas::updateOrCreate(
                [
                    'id_mahasiswa' => $mhs->id,
                    'angkatan_kelas_id' => $matchedAk->id,
                ],
                [
                    'id_mahasiswa' => $mhs->id,
                    'angkatan_kelas_id' => $matchedAk->id,
                    'prodi_id' => $prodiTrk->id,
                ]
            );
        }
    }
}
