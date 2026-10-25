<?php

namespace Database\Seeders;

use App\Models\Angkatan;
use Illuminate\Database\Seeder;

class AngkatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['angkatan' => 2023],
            ['angkatan' => 2024],
            ['angkatan' => 2025],
            ['angkatan' => 2026],
        ];

        foreach ($data as $item) {
            Angkatan::updateOrCreate(['angkatan' => $item['angkatan']], $item);
        }
    }
}
