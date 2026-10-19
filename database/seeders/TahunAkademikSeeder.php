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
            ['tahun' => 2021],
            ['tahun' => 2022],
            ['tahun' => 2023],
            ['tahun' => 2024],
            ['tahun' => 2025],
            ['tahun' => 2026],
        ];

        foreach ($data as $item) {
            TahunAkademik::firstOrCreate(['tahun' => $item['tahun']], $item);
        }
    }
}
