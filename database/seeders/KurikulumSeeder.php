<?php

namespace Database\Seeders;

use App\Enums\StatusKurikulum;
use App\Models\Kurikulum;
use App\Models\Prodi;
use Illuminate\Database\Seeder;

class KurikulumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $prodiTrk = Prodi::where('kode_prodi', 'PR01')->first() ?? Prodi::first();

        if (! $prodiTrk) {
            return;
        }

        Kurikulum::updateOrCreate(
            ['nama_kurikulum' => 'Kurikulum Sarjana Terapan Teknologi Rekayasa Komputer'],
            [
                'id_prodi' => $prodiTrk->id,
                'nama_kurikulum' => 'Kurikulum Sarjana Terapan Teknologi Rekayasa Komputer',
                'tahun_mulai' => 2022,
                'berlaku_sampai' => 2028,
                'status' => StatusKurikulum::AKTIF,
                'dokumen_url' => 'https://siakre.polines.ac.id/dokumen/kurikulum-trk-2022.pdf',
                'profil_lulusan' => 'Lulusan memiliki kompetensi unggul di bidang rekayasa sistem komputer embedded, IoT, cloud computing, serta keamanan jaringan.',
                'sk_kurikulum' => 'SK/POLINES/433-RK/2022',
            ]
        );
    }
}
