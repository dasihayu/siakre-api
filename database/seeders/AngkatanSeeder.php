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
        $years = [2018, 2019, 2020, 2021, 2022, 2023, 2024, 2025, 2026];

        foreach ($years as $year) {
            Angkatan::updateOrCreate(
                ['angkatan' => $year],
                ['angkatan' => $year]
            );
        }
    }
}
