<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KegiatanMahasiswa;
use App\Models\Mahasiswa;
use App\Models\TahunAkademik;

class KegiatanMahasiswaSeeder extends Seeder
{
    public function run(): void
    {
        $mahasiswa = Mahasiswa::first();
        $tahunAkademik = TahunAkademik::first();

        if ($mahasiswa && $tahunAkademik) {
            KegiatanMahasiswa::create([
                'mahasiswa_id'      => $mahasiswa->id,
                'tahun_akademik_id' => $tahunAkademik->id,
                'nama_kegiatan'     => 'Turnamen Mobile Legends',
                'jenis_kegiatan'    => 'UKM',
                'peran'             => 'Ketua Tim',
                'tingkat'           => 'Polines',
                'tanggal_mulai'     => '2026-02-01',
                'tanggal_selesai'   => '2026-06-30',
                'penyelenggara'     => 'BEM Polines',
                'status_validasi'   => 'Disetujui',
            ]);
        }
    }
}