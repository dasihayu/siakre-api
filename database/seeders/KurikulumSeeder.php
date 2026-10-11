<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Kurikulum;

class KurikulumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Kurikulum::create([
            'id_prodi'       => 1, // Pastikan ID Prodi 1 sudah ada (dari ProdiSeeder)
            'nama_kurikulum' => 'Kurikulum Merdeka 2024',
            'berlaku_sampai' => 2028,
            'sk_kurikulum'   => 'SK/2024/001'
        ]);
    }
}
