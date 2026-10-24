<?php

namespace Database\Seeders;

use App\Models\TahunAkademik;
use Illuminate\Database\Seeder;

class TahunAkademikSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['tahun' => '2021', 'semester' => 'Ganjil', 'is_active' => false],
            ['tahun' => '2022', 'semester' => 'Genap', 'is_active' => false],
            ['tahun' => '2023', 'semester' => 'Ganjil', 'is_active' => false],
            ['tahun' => '2024', 'semester' => 'Genap', 'is_active' => false],
            ['tahun' => '2025', 'semester' => 'Ganjil', 'is_active' => true],
            ['tahun' => '2026', 'semester' => 'Genap', 'is_active' => true],
        ];

        foreach ($data as $item) {
            TahunAkademik::firstOrCreate(
                ['tahun' => $item['tahun'], 'semester' => $item['semester']],
                $item
            );
        }
    }
}
