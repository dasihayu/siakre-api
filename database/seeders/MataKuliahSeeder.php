<?php

namespace Database\Seeders;

use App\Models\Kurikulum;
use App\Models\MataKuliah;
use Illuminate\Database\Seeder;

class MataKuliahSeeder extends Seeder
{
    /**
     * Daftar mata kuliah resmi S.Tr. Teknologi Rekayasa Komputer Polines
     * Sumber: https://elektro.polines.ac.id/433-rk
     *
     * @return array<int, array{kode_mata_kuliah: string, nama_mata_kuliah: string, sks: int, semester: int}>
     */
    public static function getDaftarMataKuliah(): array
    {
        return [
            // Semester 1 (20 SKS)
            ['kode_mata_kuliah' => '432-191-101', 'nama_mata_kuliah' => 'Pengantar Teknologi Informasi', 'sks' => 2, 'semester' => 1],
            ['kode_mata_kuliah' => '432-191-102', 'nama_mata_kuliah' => 'Dasar Pemrograman', 'sks' => 3, 'semester' => 1],
            ['kode_mata_kuliah' => '432-191-103', 'nama_mata_kuliah' => 'Sistem Operasi', 'sks' => 3, 'semester' => 1],
            ['kode_mata_kuliah' => '432-191-104', 'nama_mata_kuliah' => 'Arsitektur dan Organisasi Komputer', 'sks' => 3, 'semester' => 1],
            ['kode_mata_kuliah' => '432-191-105', 'nama_mata_kuliah' => 'Komunikasi Data', 'sks' => 3, 'semester' => 1],
            ['kode_mata_kuliah' => '432-191-106', 'nama_mata_kuliah' => 'Logika dan Algoritma', 'sks' => 3, 'semester' => 1],
            ['kode_mata_kuliah' => '432-191-107', 'nama_mata_kuliah' => 'Desain Grafis', 'sks' => 3, 'semester' => 1],

            // Semester 2 (22 SKS)
            ['kode_mata_kuliah' => '432-191-201', 'nama_mata_kuliah' => 'Kalkulus', 'sks' => 2, 'semester' => 2],
            ['kode_mata_kuliah' => '432-191-202', 'nama_mata_kuliah' => 'Struktur Data', 'sks' => 3, 'semester' => 2],
            ['kode_mata_kuliah' => '432-191-203', 'nama_mata_kuliah' => 'Probabilitas dan Statistik', 'sks' => 2, 'semester' => 2],
            ['kode_mata_kuliah' => '432-191-204', 'nama_mata_kuliah' => 'Pemrograman Berbasis Objek', 'sks' => 3, 'semester' => 2],
            ['kode_mata_kuliah' => '432-191-205', 'nama_mata_kuliah' => 'Basis Data', 'sks' => 3, 'semester' => 2],
            ['kode_mata_kuliah' => '432-191-206', 'nama_mata_kuliah' => 'Agama', 'sks' => 2, 'semester' => 2],
            ['kode_mata_kuliah' => '432-191-207', 'nama_mata_kuliah' => 'Bahasa Inggris I', 'sks' => 2, 'semester' => 2],
            ['kode_mata_kuliah' => '432-191-208', 'nama_mata_kuliah' => 'Jaringan Komputer I', 'sks' => 3, 'semester' => 2],
            ['kode_mata_kuliah' => '432-191-209', 'nama_mata_kuliah' => 'Pemrograman Visual', 'sks' => 3, 'semester' => 2],

            // Semester 3 (20 SKS)
            ['kode_mata_kuliah' => '432-191-301', 'nama_mata_kuliah' => 'Kecerdasan Buatan', 'sks' => 3, 'semester' => 3],
            ['kode_mata_kuliah' => '432-191-302', 'nama_mata_kuliah' => 'Jaringan Komputer 2', 'sks' => 3, 'semester' => 3],
            ['kode_mata_kuliah' => '432-191-303', 'nama_mata_kuliah' => 'Pemrograman Perangkat Bergerak', 'sks' => 3, 'semester' => 3],
            ['kode_mata_kuliah' => '432-191-304', 'nama_mata_kuliah' => 'Matematika Diskrit', 'sks' => 2, 'semester' => 3],
            ['kode_mata_kuliah' => '432-191-305', 'nama_mata_kuliah' => 'Pemrograman Web Dinamis', 'sks' => 3, 'semester' => 3],
            ['kode_mata_kuliah' => '432-191-306', 'nama_mata_kuliah' => 'Mikroprosesor & Mikrokontroller', 'sks' => 3, 'semester' => 3],
            ['kode_mata_kuliah' => '432-191-307', 'nama_mata_kuliah' => 'Sistem Tertanam', 'sks' => 3, 'semester' => 3],

            // Semester 4 (20 SKS)
            ['kode_mata_kuliah' => '432-191-401', 'nama_mata_kuliah' => 'Pembelajaran Mesin', 'sks' => 3, 'semester' => 4],
            ['kode_mata_kuliah' => '432-191-402', 'nama_mata_kuliah' => 'Keamanan Jaringan', 'sks' => 3, 'semester' => 4],
            ['kode_mata_kuliah' => '432-191-403', 'nama_mata_kuliah' => 'Pancasila', 'sks' => 2, 'semester' => 4],
            ['kode_mata_kuliah' => '432-191-404', 'nama_mata_kuliah' => 'Pemrograman Game', 'sks' => 3, 'semester' => 4],
            ['kode_mata_kuliah' => '432-191-405', 'nama_mata_kuliah' => 'Interoperabilitas', 'sks' => 3, 'semester' => 4],
            ['kode_mata_kuliah' => '432-191-406', 'nama_mata_kuliah' => 'Pemrograman Web Berbasis Framework', 'sks' => 3, 'semester' => 4],
            ['kode_mata_kuliah' => '432-191-407', 'nama_mata_kuliah' => 'Pengolahan Citra', 'sks' => 3, 'semester' => 4],

            // Semester 5 (19 SKS)
            ['kode_mata_kuliah' => '432-191-501', 'nama_mata_kuliah' => 'Bahasa Inggris 2', 'sks' => 2, 'semester' => 5],
            ['kode_mata_kuliah' => '432-191-502', 'nama_mata_kuliah' => 'Desain dan Perancangan Sistem Informasi', 'sks' => 3, 'semester' => 5],
            ['kode_mata_kuliah' => '432-191-503', 'nama_mata_kuliah' => 'Sistem Multimedia', 'sks' => 3, 'semester' => 5],
            ['kode_mata_kuliah' => '432-191-504', 'nama_mata_kuliah' => 'Internet of Things (IoT)', 'sks' => 3, 'semester' => 5],
            ['kode_mata_kuliah' => '432-191-505', 'nama_mata_kuliah' => 'Data Mining', 'sks' => 3, 'semester' => 5],
            ['kode_mata_kuliah' => '432-191-506', 'nama_mata_kuliah' => 'Komputasi Awan', 'sks' => 3, 'semester' => 5],
            ['kode_mata_kuliah' => '432-191-507', 'nama_mata_kuliah' => 'Kewarganegaraan', 'sks' => 2, 'semester' => 5],

            // Semester 6 (20 SKS)
            ['kode_mata_kuliah' => '432-191-601', 'nama_mata_kuliah' => 'Sistem Informasi Enterprise', 'sks' => 3, 'semester' => 6],
            ['kode_mata_kuliah' => '432-191-602', 'nama_mata_kuliah' => 'Bahasa Indonesia', 'sks' => 2, 'semester' => 6],
            ['kode_mata_kuliah' => '432-191-603', 'nama_mata_kuliah' => 'Simulasi dan Pemrograman Robot', 'sks' => 3, 'semester' => 6],
            ['kode_mata_kuliah' => '432-191-604', 'nama_mata_kuliah' => 'Big Data', 'sks' => 3, 'semester' => 6],
            ['kode_mata_kuliah' => '432-191-605', 'nama_mata_kuliah' => 'Real-Time System', 'sks' => 3, 'semester' => 6],
            ['kode_mata_kuliah' => '432-191-606', 'nama_mata_kuliah' => 'Manajemen Proyek', 'sks' => 2, 'semester' => 6],
            ['kode_mata_kuliah' => '432-191-607', 'nama_mata_kuliah' => 'Metodologi Penelitian', 'sks' => 2, 'semester' => 6],
            ['kode_mata_kuliah' => '432-191-608', 'nama_mata_kuliah' => 'Technopreneurship', 'sks' => 2, 'semester' => 6],

            // Semester 7 (19 SKS)
            ['kode_mata_kuliah' => '432-191-701', 'nama_mata_kuliah' => 'Virtual System & Services', 'sks' => 3, 'semester' => 7],
            ['kode_mata_kuliah' => '432-191-702', 'nama_mata_kuliah' => 'Etika Profesi', 'sks' => 2, 'semester' => 7],
            ['kode_mata_kuliah' => '432-191-703', 'nama_mata_kuliah' => 'Integrative Programming', 'sks' => 3, 'semester' => 7],
            ['kode_mata_kuliah' => '432-191-704', 'nama_mata_kuliah' => 'Special Topic in Industrial Informatic', 'sks' => 2, 'semester' => 7],
            ['kode_mata_kuliah' => '432-191-705', 'nama_mata_kuliah' => 'Automasi Industri', 'sks' => 3, 'semester' => 7],
            ['kode_mata_kuliah' => '432-191-706', 'nama_mata_kuliah' => 'Tugas Akhir', 'sks' => 6, 'semester' => 7],

            // Semester 8 (6 SKS)
            ['kode_mata_kuliah' => '432-191-801', 'nama_mata_kuliah' => 'Magang', 'sks' => 6, 'semester' => 8],
        ];
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kurikulum = Kurikulum::where('nama_kurikulum', 'Kurikulum Sarjana Terapan Teknologi Rekayasa Komputer')->first() ?? Kurikulum::first();

        if (! $kurikulum) {
            return;
        }

        foreach (self::getDaftarMataKuliah() as $item) {
            MataKuliah::updateOrCreate(
                ['kode_mata_kuliah' => $item['kode_mata_kuliah']],
                [
                    'id_kurikulum' => $kurikulum->id,
                    'kode_mata_kuliah' => $item['kode_mata_kuliah'],
                    'nama_mata_kuliah' => $item['nama_mata_kuliah'],
                    'sks' => $item['sks'],
                ]
            );
        }
    }
}
