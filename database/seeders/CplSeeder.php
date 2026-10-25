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
        $kurikulum = Kurikulum::where('nama_kurikulum', 'Kurikulum Sarjana Terapan Teknologi Rekayasa Komputer')->first() ?? Kurikulum::first();

        if (! $kurikulum) {
            return;
        }

        $cpls = [
            [
                'kode_cpl' => 'CPL-01',
                'deskripsi_cpl' => 'Bertakwa kepada Tuhan Yang Maha Esa, menjunjung tinggi nilai kemanusiaan, Pancasila, kewarganegaraan, serta etika profesi di bidang teknologi rekayasa komputer.',
                'kategori' => 'Sikap',
            ],
            [
                'kode_cpl' => 'CPL-02',
                'deskripsi_cpl' => 'Menunjukkan sikap disiplin, tanggung jawab, kerja sama tim, dan semangat kewirausahaan teknologi (IT Preneur).',
                'kategori' => 'Sikap',
            ],
            [
                'kode_cpl' => 'CPL-03',
                'deskripsi_cpl' => 'Menguasai konsep teoretis matematika terapan, algoritma, struktur data, serta arsitektur dan organisasi sistem komputer.',
                'kategori' => 'Pengetahuan',
            ],
            [
                'kode_cpl' => 'CPL-04',
                'deskripsi_cpl' => 'Menguasai metodologi analisis dan perancangan perangkat lunak, sistem cerdas, manajemen proyek, dan sistem informasi enterprise (IT Analyst).',
                'kategori' => 'Pengetahuan',
            ],
            [
                'kode_cpl' => 'CPL-05',
                'deskripsi_cpl' => 'Mampu menerapkan pemikiran logis, kritis, sistematis, dan inovatif dalam rekayasa terapan serta penulisan karya ilmiah.',
                'kategori' => 'Keterampilan Umum',
            ],
            [
                'kode_cpl' => 'CPL-06',
                'deskripsi_cpl' => 'Mampu membangun dan mengevaluasi perangkat lunak multiplatform (Web, Mobile, Visual, Game, dan Multimedia) secara terintegrasi (Software Engineer).',
                'kategori' => 'Keterampilan Khusus',
            ],
            [
                'kode_cpl' => 'CPL-07',
                'deskripsi_cpl' => 'Mampu merancang dan mengimplementasikan konektivitas jaringan komputer, keamanan siber, komputasi awan, sistem tertanam, IoT, dan automasi industri (Network & Cloud Engineer).',
                'kategori' => 'Keterampilan Khusus',
            ],
            [
                'kode_cpl' => 'CPL-08',
                'deskripsi_cpl' => 'Mampu mengelola dan menganalisis data skala besar, teks, dan citra menggunakan teknik pembelajaran mesin serta kecerdasan buatan (Data Analysts & Scientists).',
                'kategori' => 'Keterampilan Khusus',
            ],
        ];

        foreach ($cpls as $item) {
            Cpl::updateOrCreate(
                ['kode_cpl' => $item['kode_cpl']],
                array_merge($item, ['id_kurikulum' => $kurikulum->id])
            );
        }
    }
}
