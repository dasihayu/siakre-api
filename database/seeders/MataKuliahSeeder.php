<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MataKuliah;

class MataKuliahSeeder extends Seeder
{
    public function run(): void
    {
        MataKuliah::create([
            'id_kurikulum'     => 1, // Pastikan ID Kurikulum 1 sudah ada dari KurikulumSeeder
            'kode_mata_kuliah' => 'IF101',
            'nama_mata_kuliah' => 'Pemrograman Web',
            'sks'              => 3,
        ]);
    }
}