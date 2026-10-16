<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kelas;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['kelas' => 'TI-1A'],
            ['kelas' => 'TI-1B'],
            ['kelas' => 'TI-1C'],
            ['kelas' => 'TI-2A'],
            ['kelas' => 'TI-2B'],
            ['kelas' => 'TI-3A'],
            ['kelas' => 'TI-3B'],
            ['kelas' => 'TI-4A'],
            ['kelas' => 'TI-4B'],
        ];

        foreach ($data as $item) {
            Kelas::firstOrCreate(['kelas' => $item['kelas']], $item);
        }
    }
}