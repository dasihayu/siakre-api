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
            ['tahun' => '2022/2023', 'semester' => 'Ganjil', 'is_active' => false],
            ['tahun' => '2022/2023', 'semester' => 'Genap', 'is_active' => false],
            ['tahun' => '2023/2024', 'semester' => 'Ganjil', 'is_active' => false],
            ['tahun' => '2023/2024', 'semester' => 'Genap', 'is_active' => false],
            ['tahun' => '2024/2025', 'semester' => 'Ganjil', 'is_active' => false],
            ['tahun' => '2024/2025', 'semester' => 'Genap', 'is_active' => false],
            ['tahun' => '2025/2026', 'semester' => 'Ganjil', 'is_active' => false],
            ['tahun' => '2025/2026', 'semester' => 'Genap', 'is_active' => false],
            ['tahun' => '2026/2027', 'semester' => 'Ganjil', 'is_active' => true],
            ['tahun' => '2026/2027', 'semester' => 'Genap', 'is_active' => false],
        ];

        foreach ($data as $item) {
            TahunAkademik::updateOrCreate(
                ['tahun' => $item['tahun'], 'semester' => $item['semester']],
                $item
            );
        }
    }
}
