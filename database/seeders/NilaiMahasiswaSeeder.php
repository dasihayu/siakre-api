<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NilaiMahasiswa;

class NilaiMahasiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'enrollment_mahasiswa_angkatan_kelas_id' => 1,
                'mata_kuliah_angkatan_kelas_id' => 1,
                'nilai_angka' => 85.50,
                'nilai_huruf' => 'A',
                'bobot_nilai' => 4.00,
                'status_kelulusan' => 'Lulus',
            ],
            [
                'enrollment_mahasiswa_angkatan_kelas_id' => 2,
                'mata_kuliah_angkatan_kelas_id' => 1,
                'nilai_angka' => 75.00,
                'nilai_huruf' => 'B',
                'bobot_nilai' => 3.00,
                'status_kelulusan' => 'Lulus',
            ],
            [
                'enrollment_mahasiswa_angkatan_kelas_id' => 3,
                'mata_kuliah_angkatan_kelas_id' => 2,
                'nilai_angka' => 45.00,
                'nilai_huruf' => 'E',
                'bobot_nilai' => 0.00,
                'status_kelulusan' => 'Tidak Lulus',
            ],
        ];

        foreach ($data as $item) {
            NilaiMahasiswa::firstOrCreate(
                [
                    'enrollment_mahasiswa_angkatan_kelas_id' => $item['enrollment_mahasiswa_angkatan_kelas_id'],
                    'mata_kuliah_angkatan_kelas_id' => $item['mata_kuliah_angkatan_kelas_id'],
                ],
                $item
            );
        }
    }
}