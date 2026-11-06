<?php

namespace Database\Seeders;

use App\Enums\JenisEvidencePrestasi;
use App\Models\EvidencePrestasiMahasiswa;
use App\Models\PrestasiMahasiswa;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class EvidencePrestasiMahasiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $allPrestasi = PrestasiMahasiswa::all();

        foreach ($allPrestasi as $prestasi) {
            $slug = Str::slug($prestasi->nama_aktivitas, '-');

            // 1. Evidence Utama: Sertifikat Prestasi
            EvidencePrestasiMahasiswa::firstOrCreate(
                [
                    'id_prestasi_mahasiswa' => $prestasi->id,
                    'url' => "https://storage.polines.ac.id/prestasi/{$slug}-{$prestasi->tahun}.pdf",
                ],
                [
                    'id_prestasi_mahasiswa' => $prestasi->id,
                    'jenis_evidence' => JenisEvidencePrestasi::SERTIFIKAT->value,
                    'judul' => "Sertifikat {$prestasi->nama_aktivitas}",
                    'url' => "https://storage.polines.ac.id/prestasi/{$slug}-{$prestasi->tahun}.pdf",
                ]
            );

            // 2. Evidence Tambahan: Link Dokumentasi / Lomba (Multi-Link)
            EvidencePrestasiMahasiswa::firstOrCreate(
                [
                    'id_prestasi_mahasiswa' => $prestasi->id,
                    'url' => "https://kemahasiswaan.polines.ac.id/dokumentasi/{$slug}",
                ],
                [
                    'id_prestasi_mahasiswa' => $prestasi->id,
                    'jenis_evidence' => JenisEvidencePrestasi::FOTO_KEGIATAN->value,
                    'judul' => "Dokumentasi Foto Kegiatan {$prestasi->nama_aktivitas}",
                    'url' => "https://kemahasiswaan.polines.ac.id/dokumentasi/{$slug}",
                ]
            );
        }
    }
}
