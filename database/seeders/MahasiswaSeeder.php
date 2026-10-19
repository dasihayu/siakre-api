<?php

namespace Database\Seeders;

use App\Models\Mahasiswa;
use Illuminate\Database\Seeder;

class MahasiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mahasiswaList = [
            [
                'nim' => 33424101,
                'nama_mahasiswa' => 'Andrian Amirudin',
                'tahun_masuk' => 2024,
                'jenis_pendaftaran' => 'Reguler',
                'tanggal_kelulusan' => null,
                'masa_studi_bulan' => 48,
            ],
            [
                'nim' => 43325006,
                'nama_mahasiswa' => 'Dasi Hayu Permana',
                'tahun_masuk' => 2024,
                'jenis_pendaftaran' => 'SNBT',
                'tanggal_kelulusan' => null,
                'masa_studi_bulan' => 48,
            ],
            [
                'nim' => 43325002,
                'nama_mahasiswa' => 'Andrian Amirudin Rahmanto',
                'tahun_masuk' => 2024,
                'jenis_pendaftaran' => 'Reguler',
                'tanggal_kelulusan' => null,
                'masa_studi_bulan' => 48,
            ],
            [
                'nim' => 43325003,
                'nama_mahasiswa' => 'Annisa Nur Farikha',
                'tahun_masuk' => 2024,
                'jenis_pendaftaran' => 'SNBP',
                'tanggal_kelulusan' => null,
                'masa_studi_bulan' => 48,
            ],
            [
                'nim' => 43325004,
                'nama_mahasiswa' => 'Choirul Raissa Pasha',
                'tahun_masuk' => 2024,
                'jenis_pendaftaran' => 'SNBT',
                'tanggal_kelulusan' => null,
                'masa_studi_bulan' => 48,
            ],
            [
                'nim' => 43325019,
                'nama_mahasiswa' => 'Pranaja Luthfi Belian',
                'tahun_masuk' => 2024,
                'jenis_pendaftaran' => 'Mandiri',
                'tanggal_kelulusan' => null,
                'masa_studi_bulan' => 48,
            ],
            [
                'nim' => 43325001,
                'nama_mahasiswa' => 'Aisa Reta Lestari',
                'tahun_masuk' => 2024,
                'jenis_pendaftaran' => 'SNBP',
                'tanggal_kelulusan' => null,
                'masa_studi_bulan' => 48,
            ],
            [
                'nim' => 43325007,
                'nama_mahasiswa' => 'Dimas Bayu Pratama',
                'tahun_masuk' => 2024,
                'jenis_pendaftaran' => 'Reguler',
                'tanggal_kelulusan' => null,
                'masa_studi_bulan' => 48,
            ],
            [
                'nim' => 43325008,
                'nama_mahasiswa' => 'Dzidan Adrian Firmansyah',
                'tahun_masuk' => 2024,
                'jenis_pendaftaran' => 'SNBT',
                'tanggal_kelulusan' => null,
                'masa_studi_bulan' => 48,
            ],
            [
                'nim' => 43323010,
                'nama_mahasiswa' => 'Rizky Fauzan Hidayat',
                'tahun_masuk' => 2023,
                'jenis_pendaftaran' => 'SNBP',
                'tanggal_kelulusan' => null,
                'masa_studi_bulan' => 48,
            ],
            [
                'nim' => 43323015,
                'nama_mahasiswa' => 'Nabila Putri Cahyani',
                'tahun_masuk' => 2023,
                'jenis_pendaftaran' => 'SNBT',
                'tanggal_kelulusan' => null,
                'masa_studi_bulan' => 48,
            ],
            [
                'nim' => 43322005,
                'nama_mahasiswa' => 'Bagas Wicaksono',
                'tahun_masuk' => 2022,
                'jenis_pendaftaran' => 'Reguler',
                'tanggal_kelulusan' => '2026-08-25',
                'masa_studi_bulan' => 48,
            ],
        ];

        foreach ($mahasiswaList as $item) {
            Mahasiswa::firstOrCreate(
                ['nim' => $item['nim']],
                $item
            );
        }
    }
}
