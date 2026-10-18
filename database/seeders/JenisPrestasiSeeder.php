<?php

namespace Database\Seeders;

use App\Models\JenisPrestasi;
use Illuminate\Database\Seeder;

class JenisPrestasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        JenisPrestasi::create([
            'id_prestasi_mahasiswa' => 1,
            'jenis' => 'Akademik',
        ]);
    }
}
