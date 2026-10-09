<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cpl;

class CplSeeder extends Seeder
{
    public function run(): void
    {
        Cpl::create([
            'id_kurikulum'  => 1, // Pastikan ID Kurikulum 1 sudah ada dari KurikulumSeeder
            'kode_cpl'      => 'CPL-01',
            'deskripsi_cpl' => 'Mampu merancang perangkat lunak',
            'kategori'      => 'Umum'
        ]);
    }
}