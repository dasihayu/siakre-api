<?php

namespace Database\Seeders;

use App\Models\Mahasiswa;
use App\Models\PrestasiMahasiswa;
use Illuminate\Database\Seeder;

class PrestasiMahasiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mahasiswaList = Mahasiswa::all();
        if ($mahasiswaList->isEmpty()) {
            return;
        }

        $prestasiData = [
            [
                'nim' => 43325006,
                'nama_aktivitas' => 'Juara 1 Hackathon Nasional IoT & Smart City',
                'tahun' => 2025,
            ],
            [
                'nim' => 43325002,
                'nama_aktivitas' => 'Juara 2 Pagelaran Mahasiswa Nasional Bidang TIK (GEMASTIK) XVII',
                'tahun' => 2025,
            ],
            [
                'nim' => 43325003,
                'nama_aktivitas' => 'Medali Emas National Polytechnic English Olympics (NPEO)',
                'tahun' => 2025,
            ],
            [
                'nim' => 43325004,
                'nama_aktivitas' => 'Juara 1 Lomba Cipta Perangkat Lunak Vokasi Nasional',
                'tahun' => 2025,
            ],
            [
                'nim' => 43325019,
                'nama_aktivitas' => 'Finalis Huawei ICT Competition Regional Asia Pacific',
                'tahun' => 2026,
            ],
            [
                'nim' => 43324001,
                'nama_aktivitas' => 'Juara 3 Kompetisi Cyber Defense & Security Nasional',
                'tahun' => 2025,
            ],
            [
                'nim' => 43323001,
                'nama_aktivitas' => 'Juara 1 Kontes Robot Indonesia (KRI) Tingkat Wilayah',
                'tahun' => 2025,
            ],
        ];

        foreach ($prestasiData as $item) {
            $mhs = Mahasiswa::where('nim', $item['nim'])->first() ?? $mahasiswaList->first();

            PrestasiMahasiswa::updateOrCreate(
                [
                    'id_mahasiswa' => $mhs->id,
                    'nama_aktivitas' => $item['nama_aktivitas'],
                ],
                [
                    'id_mahasiswa' => $mhs->id,
                    'nama_aktivitas' => $item['nama_aktivitas'],
                    'tahun' => $item['tahun'],
                ]
            );
        }
    }
}
