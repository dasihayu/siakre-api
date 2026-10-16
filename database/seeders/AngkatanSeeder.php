<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Angkatan;

class AngkatanSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['angkatan' => 2021],
            ['angkatan' => 2022],
            ['angkatan' => 2023],
            ['angkatan' => 2024],
            ['angkatan' => 2025],
        ];

        foreach ($data as $item) {
            Angkatan::firstOrCreate(['angkatan' => $item['angkatan']], $item);
        }
    }
}