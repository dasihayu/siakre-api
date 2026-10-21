<?php

namespace Database\Seeders;

use App\Models\MataKuliahAngkatanKelas;
use App\Models\NilaiMahasiswa;
use Illuminate\Database\Seeder;

class NilaiMahasiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courses = MataKuliahAngkatanKelas::all();

        if ($courses->isEmpty()) {
            return;
        }

        foreach ($courses as $course) {
            NilaiMahasiswa::firstOrCreate(
                [
                    'mata_kuliah_angkatan_kelas_id' => $course->id,
                ],
                [
                    'mata_kuliah_angkatan_kelas_id' => $course->id,
                    'link_nilai' => "https://docs.google.com/spreadsheets/d/e/2PACX-1vNilaiMahasiswa_{$course->id}/pubhtml",
                    'link_evidence' => "https://drive.google.com/drive/folders/EvidenceNilai_{$course->id}",
                ]
            );
        }
    }
}
