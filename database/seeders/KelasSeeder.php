<?php

namespace Database\Seeders;

use App\Models\Kelas;
use Illuminate\Database\Seeder;

class KelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['kelas' => 'TI-1A'],
            ['kelas' => 'TI-1B'],
            ['kelas' => 'TI-1C'],
            ['kelas' => 'TI-2A'],
            ['kelas' => 'TI-2B'],
            ['kelas' => 'TI-2C'],
            ['kelas' => 'TI-3A'],
            ['kelas' => 'TI-3B'],
            ['kelas' => 'TI-3C'],
            ['kelas' => 'TI-4A'],
            ['kelas' => 'TI-4B'],
            ['kelas' => 'TI-4C'],
        ];

        foreach ($data as $item) {
            Kelas::updateOrCreate(['kelas' => $item['kelas']], $item);
        }
    }
}
