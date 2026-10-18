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
    public function run(): void
    {
        $kelas = Kelas::first();
        $angkatan = Angkatan::first();
        $prodi = Prodi::first();
        $ta = TahunAkademik::first();

        if ($kelas && $angkatan && $prodi && $ta) {
            AngkatanKelas::create([
                'kelas_id' => $kelas->id,
                'angkatan_id' => $angkatan->id,
                'prodi_id' => $prodi->id,
                'tahun_akademik_id' => $ta->id,
            ]);
        }
    }
}
