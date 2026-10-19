<?php

namespace Database\Seeders;

use App\Models\Kurikulum;
use App\Models\MataKuliah;
use Illuminate\Database\Seeder;

class MataKuliahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kurikulum = Kurikulum::where('nama_kurikulum', 'Kurikulum Merdeka TRK 2024')->first() ?? Kurikulum::first();

        if (! $kurikulum) {
            return;
        }

        $mataKuliahs = [
            [
                'kode_mata_kuliah' => 'IF101',
                'nama_mata_kuliah' => 'Pemrograman Web',
                'sks' => 3,
            ],
            [
                'kode_mata_kuliah' => 'TRK101',
                'nama_mata_kuliah' => 'Algoritma dan Pemrograman Terapan',
                'sks' => 3,
            ],
            [
                'kode_mata_kuliah' => 'TRK102',
                'nama_mata_kuliah' => 'Basis Data dan Desain Sistem Terdistribusi',
                'sks' => 3,
            ],
            [
                'kode_mata_kuliah' => 'TRK103',
                'nama_mata_kuliah' => 'Arsitektur Komputer dan Antarmuka',
                'sks' => 3,
            ],
            [
                'kode_mata_kuliah' => 'TRK104',
                'nama_mata_kuliah' => 'Sistem Operasi dan Administrasi Linux',
                'sks' => 3,
            ],
            [
                'kode_mata_kuliah' => 'TRK201',
                'nama_mata_kuliah' => 'Pemrograman Web Lanjut & API Architecture',
                'sks' => 3,
            ],
            [
                'kode_mata_kuliah' => 'TRK202',
                'nama_mata_kuliah' => 'Jaringan Komputer dan Protokol Komunikasi',
                'sks' => 3,
            ],
            [
                'kode_mata_kuliah' => 'TRK203',
                'nama_mata_kuliah' => 'Sistem Tertanam dan Sensor Internet of Things (IoT)',
                'sks' => 4,
            ],
            [
                'kode_mata_kuliah' => 'TRK204',
                'nama_mata_kuliah' => 'Rekayasa Perangkat Lunak Berorientasi Objek',
                'sks' => 3,
            ],
            [
                'kode_mata_kuliah' => 'TRK301',
                'nama_mata_kuliah' => 'Kecerdasan Buatan dan Pembelajaran Mesin',
                'sks' => 3,
            ],
            [
                'kode_mata_kuliah' => 'TRK302',
                'nama_mata_kuliah' => 'Keamanan Jaringan Komputer dan Kriptografi',
                'sks' => 3,
            ],
            [
                'kode_mata_kuliah' => 'TRK401',
                'nama_mata_kuliah' => 'Proyek Rekayasa Sistem Komputer Terapan',
                'sks' => 4,
            ],
        ];

        foreach ($mataKuliahs as $item) {
            MataKuliah::firstOrCreate(
                ['kode_mata_kuliah' => $item['kode_mata_kuliah']],
                array_merge($item, ['id_kurikulum' => $kurikulum->id])
            );
        }
    }
}
