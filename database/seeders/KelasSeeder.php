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
            ['kelas' => 'TRK-1A'],
            ['kelas' => 'TRK-1B'],
            ['kelas' => 'TRK-2A'],
            ['kelas' => 'TRK-2B'],
            ['kelas' => 'TRK-3A'],
            ['kelas' => 'TRK-3B'],
            ['kelas' => 'TRK-4A'],
            ['kelas' => 'TRK-4B'],
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
