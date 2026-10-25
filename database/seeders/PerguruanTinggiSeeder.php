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
        PerguruanTinggi::updateOrCreate(
            ['kode_pt' => 'PT001'],
            [
                'kode_pt' => 'PT001',
                'nama_pt' => 'Politeknik Negeri Semarang',
                'alamat' => 'Jl. Prof. H. Soedarto, S.H., Tembalang, Semarang 50275',
                'pimpinan' => 'Dr. Eni Dwi Wardihani, S.T., M.T.',
                'visi' => 'Menjadi perguruan tinggi vokasi terkemuka di Asia Tenggara yang adaptif, inovatif, dan berdaya saing global pada tahun 2029.',
                'misi' => 'Menyelenggarakan pendidikan terapan berkualitas internasional serta riset terapan inovatif untuk industri.',
            ]
        );
    }
}
