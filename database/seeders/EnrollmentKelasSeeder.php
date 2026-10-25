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
     *
     * Memetakan mahasiswa ke setiap AngkatanKelas pada seluruh Tahun Akademik
     * sesuai tahun angkatan (tahun_masuk) dan paralel kelasnya (A, B, atau C).
     * Dengan demikian:
     * - Di setiap Tahun Akademik selalu ada 12 kelas (TI-1A s/d TI-4C) @ 25 mahasiswa.
     * - Mahasiswa yang sama naik kelas dari TI-1* -> TI-2* -> TI-3* -> TI-4* seiring bergantinya Tahun Akademik.
     */
    public function run(): void
    {
        $prodiTrk = Prodi::where('kode_prodi', 'PR01')->first() ?? Prodi::first();
        if (! $prodiTrk) {
            return;
        }

        $allMahasiswa = Mahasiswa::all();
        $angkatanKelasList = AngkatanKelas::with(['kelas', 'angkatan'])->get();

        if ($allMahasiswa->isEmpty() || $angkatanKelasList->isEmpty()) {
            return;
        }

        $bToHuruf = [
            '0' => 'A',
            '1' => 'B',
            '2' => 'C',
        ];

        // Group AngkatanKelas by "{tahun_angkatan}|{paralel_huruf}" (e.g. "2025|A")
        $akByCohortAndLetter = $angkatanKelasList->groupBy(function (AngkatanKelas $ak) {
            $tahunAngkatan = (int) ($ak->angkatan?->angkatan ?? 0);
            $namaKelas = (string) ($ak->kelas?->kelas ?? '');
            $huruf = strtoupper(substr($namaKelas, -1));

            return "{$tahunAngkatan}|{$huruf}";
        });

        EnrollmentKelas::query()->delete();

        $now = now();
        $records = [];

        foreach ($allMahasiswa as $mhs) {
            $nimStr = (string) $mhs->nim;
            $huruf = 'A';

            // Format NIM: 433 + aa (2 digit) + b (1 digit) + cc (2 digit)
            if (strlen($nimStr) === 8 && str_starts_with($nimStr, '433')) {
                $b = substr($nimStr, 5, 1);
                $huruf = $bToHuruf[$b] ?? 'A';
            }

            $key = "{$mhs->tahun_masuk}|{$huruf}";
            $matchedAks = $akByCohortAndLetter->get($key, collect());

            foreach ($matchedAks as $ak) {
                $records[] = [
                    'id_mahasiswa' => $mhs->id,
                    'angkatan_kelas_id' => $ak->id,
                    'prodi_id' => $prodiTrk->id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        foreach (array_chunk($records, 500) as $chunk) {
            EnrollmentKelas::insert($chunk);
        }
    }
}
