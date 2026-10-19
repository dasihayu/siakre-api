<?php

namespace Database\Seeders;

use App\Models\PerguruanTinggi;
use Illuminate\Database\Seeder;

class PerguruanTinggiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $institutions = [
            [
                'kode_pt' => 'PT001',
                'nama_pt' => 'Politeknik Negeri Semarang',
                'alamat' => 'Jl. Prof. H. Soedarto, S.H., Tembalang, Semarang 50275',
                'pimpinan' => 'Dr. Eni Dwi Wardihani, S.T., M.T.',
                'visi' => 'Menjadi perguruan tinggi vokasi terkemuka di Asia Tenggara yang adaptif, inovatif, dan berdaya saing global pada tahun 2029.',
                'misi' => 'Menyelenggarakan pendidikan terapan berkualitas internasional serta riset terapan inovatif untuk industri.',
            ],
            [
                'kode_pt' => 'PT002',
                'nama_pt' => 'Politeknik Elektronika Negeri Surabaya',
                'alamat' => 'Jl. Raya ITS Kampus PENS Sukolilo, Surabaya 60111',
                'pimpinan' => 'Ali Ridho Barakbah, S.Kom., Ph.D.',
                'visi' => 'Menjadi pusat keunggulan pendidikan vokasi rekayasa dan teknologi terapan terdepan di tingkat global.',
                'misi' => 'Mencetak sumber daya manusia berdaya saing tinggi dalam teknologi informasi dan elektronika.',
            ],
        ];

        foreach ($institutions as $item) {
            PerguruanTinggi::firstOrCreate(
                ['kode_pt' => $item['kode_pt']],
                $item
            );
        }
    }
}
