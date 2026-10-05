<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PerguruanTinggi;

class PerguruanTinggiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PerguruanTinggi::create([
            'kode_pt' => 'PT001',
            'nama_pt' => 'Politeknik Negeri Semarang',
            'alamat'  => 'Jl. Prof. Sudarto SH, Tembalang, Semarang',
            'pimpinan'=> 'Dyonisius Beti',
            'visi'    => 'https://polines.ac.id/visi',
            'misi'    => 'https://polines.ac.id/misi'
        ]);
    }
}
