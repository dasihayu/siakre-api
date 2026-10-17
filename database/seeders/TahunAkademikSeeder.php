<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TahunAkademik;

class TahunAkademikSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
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