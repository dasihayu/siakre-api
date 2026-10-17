<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AnggotaUpps;
use App\Models\Upps;

class AnggotaUppsSeeder extends Seeder
{
    public function run(): void
    {
        $upps = Upps::first();

        if ($upps) {
            AnggotaUpps::create([
                'id_upps' => $upps->id,
                'nama'    => 'Dr. Siti Rahma, M.T.',
                'jabatan' => 'Wakil Dekan I',
            ]);
        }
    }
}