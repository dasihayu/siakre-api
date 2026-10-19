<?php

namespace Database\Seeders;

use App\Models\Cpl;
use App\Models\Kurikulum;
use Illuminate\Database\Seeder;

class CplSeeder extends Seeder
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

        $cpls = [
            [
                'kode_cpl' => 'CPL-01',
                'deskripsi_cpl' => 'Bertakwa kepada Tuhan YME dan mampu menunjukkan sikap religius serta etika moral profesi rekayasa.',
                'kategori' => 'Sikap',
            ],
            [
                'kode_cpl' => 'CPL-02',
                'deskripsi_cpl' => 'Menunjukkan sikap disiplin, berintegritas, dan bertanggung jawab atas pekerjaan di bidang teknologi rekayasa komputer.',
                'kategori' => 'Sikap',
            ],
            [
                'kode_cpl' => 'CPL-03',
                'deskripsi_cpl' => 'Menguasai konsep teoretis matematika terapan, sains komputasi, serta arsitektur dan organisasi sistem komputer.',
                'kategori' => 'Pengetahuan',
            ],
            [
                'kode_cpl' => 'CPL-04',
                'deskripsi_cpl' => 'Menguasai prinsip dasar rekayasa perangkat lunak, sistem cerdas, dan keamanan infrastruktur siber.',
                'kategori' => 'Pengetahuan',
            ],
            [
                'kode_cpl' => 'CPL-05',
                'deskripsi_cpl' => 'Mampu menerapkan pemikiran logis, kritis, sistematis, dan inovatif dalam implementasi solusi komputasi terapan.',
                'kategori' => 'Keterampilan Umum',
            ],
            [
                'kode_cpl' => 'CPL-06',
                'deskripsi_cpl' => 'Mampu merancang, mengimplementasikan, dan menguji embedded system serta arsitektur Internet of Things (IoT).',
                'kategori' => 'Keterampilan Khusus',
            ],
            [
                'kode_cpl' => 'CPL-07',
                'deskripsi_cpl' => 'Mampu merancang arsitektur dan membangun aplikasi perangkat lunak skala industri berbasis Web, Mobile, dan API.',
                'kategori' => 'Keterampilan Khusus',
            ],
            [
                'kode_cpl' => 'CPL-08',
                'deskripsi_cpl' => 'Mampu mengonfigurasi, menganalisis performa, dan memitigasi celah keamanan jaringan komputer tingkat lanjut.',
                'kategori' => 'Keterampilan Khusus',
            ],
        ];

        foreach ($cpls as $item) {
            Cpl::firstOrCreate(
                ['kode_cpl' => $item['kode_cpl']],
                array_merge($item, ['id_kurikulum' => $kurikulum->id])
            );
        }
    }
}
