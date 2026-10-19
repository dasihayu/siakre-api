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
        $angkatanKelasMap = AngkatanKelas::with(['kelas', 'angkatan'])->get();

        if ($angkatanKelasMap->isEmpty()) {
            return;
        }

        foreach ($allMahasiswa as $mhs) {
            // Tentukan kelas berdasarkan tahun masuk mahasiswa
            $targetAngkatan = $mhs->tahun_masuk;
            $matchedAk = $angkatanKelasMap->first(function ($ak) use ($targetAngkatan) {
                return $ak->angkatan?->angkatan == $targetAngkatan;
            }) ?? $angkatanKelasMap->first();

            EnrollmentKelas::firstOrCreate(
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
