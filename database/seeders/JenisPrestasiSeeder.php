<?php

namespace Database\Seeders;

use App\Models\JenisPrestasi;
use Illuminate\Database\Seeder;

class JenisPrestasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'kode' => 'JP01',
                'nama' => 'Juara 1 Lomba Pemrograman Nasional',
                'kategori' => 'Akademik',
                'tingkat' => 'Nasional',
                'is_active' => true,
            ],
            [
                'kode' => 'JP02',
                'nama' => 'Juara 2 Hackathon Internasional',
                'kategori' => 'Akademik',
                'tingkat' => 'Internasional',
                'is_active' => true,
            ],
            [
                'kode' => 'JP03',
                'nama' => 'Juara 1 Kejuaraan Karate Regional',
                'kategori' => 'Non-Akademik',
                'tingkat' => 'Regional',
                'is_active' => true,
            ],
        ];

        foreach ($data as $item) {
            JenisPrestasi::updateOrCreate(
                ['kode' => $item['kode']],
                $item
            );
        }
    }
}