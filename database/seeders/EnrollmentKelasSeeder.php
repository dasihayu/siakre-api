<?php

namespace Database\Seeders;

use App\Models\AngkatanKelas;
use App\Models\EnrollmentKelas;
use App\Models\Mahasiswa;
use App\Models\Prodi;
use Illuminate\Database\Seeder;

class EnrollmentKelasSeeder extends Seeder
{
    public function run(): void
    {
        $mahasiswa = Mahasiswa::first();
        $angkatanKelas = AngkatanKelas::first();
        $prodi = Prodi::first();

        if ($mahasiswa && $angkatanKelas && $prodi) {
            EnrollmentKelas::create([
                'id_mahasiswa' => $mahasiswa->id,
                'angkatan_kelas_id' => $angkatanKelas->id,
                'prodi_id' => $prodi->id,
            ]);
        }
    }
}
