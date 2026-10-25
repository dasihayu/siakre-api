<?php

namespace Database\Seeders;

use App\Models\AnggotaUpps;
use App\Models\Upps;
use Illuminate\Database\Seeder;

class AnggotaUppsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $upps = Upps::where('kode_upps', 'UPPS01')->first() ?? Upps::first();

        if (! $upps) {
            return;
        }

        $anggota = [
            [
                'nama' => 'Dr. Amin Suharjono, S.T., M.T.',
                'jabatan' => 'Ketua Jurusan Teknik Elektro',
            ],
            [
                'nama' => 'Kuwat Santoso, S.T., M.Kom.',
                'jabatan' => 'Sekretaris Jurusan Teknik Elektro',
            ],
            [
                'nama' => 'Helmy, S.T., M.T.',
                'jabatan' => 'Koordinator Program Studi Teknologi Rekayasa Komputer',
            ],
            [
                'nama' => 'Bambang Supriyanto, S.T., M.T.',
                'jabatan' => 'Ketua Unit Penjaminan Mutu UPPS TRK',
            ],
            [
                'nama' => 'Agus Rochadi, S.T., M.T.',
                'jabatan' => 'Kepala Laboratorium Komputer dan Jaringan',
            ],
            [
                'nama' => 'Mardiyono, S.Kom., M.Sc., Ph.D.',
                'jabatan' => 'Kepala Laboratorium Sistem Cerdas dan Rekayasa Perangkat Lunak',
            ],
        ];

        foreach ($anggota as $item) {
            AnggotaUpps::updateOrCreate(
                [
                    'id_upps' => $upps->id,
                    'nama' => $item['nama'],
                ],
                [
                    'id_upps' => $upps->id,
                    'nama' => $item['nama'],
                    'jabatan' => $item['jabatan'],
                ]
            );
        }
    }
}
