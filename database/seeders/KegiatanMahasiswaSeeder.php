<?php

namespace Database\Seeders;

use App\Models\KegiatanMahasiswa;
use App\Models\Mahasiswa;
use App\Models\TahunAkademik;
use Illuminate\Database\Seeder;

class KegiatanMahasiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ta2024 = TahunAkademik::where('tahun', 2024)->first() ?? TahunAkademik::first();
        if (! $ta2024) {
            return;
        }

        $activities = [
            [
                'nim' => 43325006,
                'nama_kegiatan' => 'Divisi Riset Himpunan Mahasiswa Elektro',
                'jenis_kegiatan' => 'Organisasi',
                'peran' => 'Ketua Divisi',
                'tingkat' => 'Polines',
                'tanggal_mulai' => '2024-02-01',
                'tanggal_selesai' => '2024-12-20',
                'penyelenggara' => 'HME Polines',
                'status_validasi' => 'Disetujui',
            ],
            [
                'nim' => 43325002,
                'nama_kegiatan' => 'Lomba Desain Embedded System Nasional',
                'jenis_kegiatan' => 'Lomba',
                'peran' => 'Ketua Tim',
                'tingkat' => 'Nasional',
                'tanggal_mulai' => '2024-05-10',
                'tanggal_selesai' => '2024-05-12',
                'penyelenggara' => 'Kemendikbudristek',
                'status_validasi' => 'Disetujui',
            ],
            [
                'nim' => 43325003,
                'nama_kegiatan' => 'Workshop UI/UX dan Mobile App Development',
                'jenis_kegiatan' => 'Seminar',
                'peran' => 'Peserta',
                'tingkat' => 'Regional',
                'tanggal_mulai' => '2024-03-15',
                'tanggal_selesai' => '2024-03-16',
                'penyelenggara' => 'GDG Semarang',
                'status_validasi' => 'Disetujui',
            ],
            [
                'nim' => 43325004,
                'nama_kegiatan' => 'Pelatihan Cyber Security & Penetration Testing',
                'jenis_kegiatan' => 'Pelatihan',
                'peran' => 'Peserta',
                'tingkat' => 'Nasional',
                'tanggal_mulai' => '2024-07-01',
                'tanggal_selesai' => '2024-07-05',
                'penyelenggara' => 'BSSN',
                'status_validasi' => 'Disetujui',
            ],
            [
                'nim' => 43325019,
                'nama_kegiatan' => 'UKM Robotika Polines - Divisi Robot SAR',
                'jenis_kegiatan' => 'UKM',
                'peran' => 'Anggota Tim',
                'tingkat' => 'Polines',
                'tanggal_mulai' => '2024-01-10',
                'tanggal_selesai' => '2024-11-30',
                'penyelenggara' => 'UKM Robotika Polines',
                'status_validasi' => 'Disetujui',
            ],
            [
                'nim' => 43325001,
                'nama_kegiatan' => 'Pengabdian Masyarakat Teknologi Tepat Guna Desa Binaan',
                'jenis_kegiatan' => 'Pengabdian',
                'peran' => 'Koordinator Lapangan',
                'tingkat' => 'Regional',
                'tanggal_mulai' => '2024-08-01',
                'tanggal_selesai' => '2024-08-20',
                'penyelenggara' => 'P3M Polines',
                'status_validasi' => 'Disetujui',
            ],
            [
                'nim' => 43325007,
                'nama_kegiatan' => 'Program Magang Industri Otomasi IoT Terapan',
                'jenis_kegiatan' => 'Magang',
                'peran' => 'Peserta Magang',
                'tingkat' => 'Nasional',
                'tanggal_mulai' => '2024-09-01',
                'tanggal_selesai' => '2025-01-31',
                'penyelenggara' => 'PT Telkom Indonesia',
                'status_validasi' => 'Disetujui',
            ],
            [
                'nim' => 43325008,
                'nama_kegiatan' => 'Turnamen E-Sport Championship Polines',
                'jenis_kegiatan' => 'UKM',
                'peran' => 'Ketua Tim',
                'tingkat' => 'Polines',
                'tanggal_mulai' => '2024-02-01',
                'tanggal_selesai' => '2024-02-15',
                'penyelenggara' => 'BEM Polines',
                'status_validasi' => 'Disetujui',
            ],
            [
                'nim' => 33424101,
                'nama_kegiatan' => 'Turnamen Mobile Legends',
                'jenis_kegiatan' => 'UKM',
                'peran' => 'Ketua Tim',
                'tingkat' => 'Polines',
                'tanggal_mulai' => '2024-02-01',
                'tanggal_selesai' => '2024-06-30',
                'penyelenggara' => 'BEM Polines',
                'status_validasi' => 'Disetujui',
            ],
        ];

        foreach ($activities as $act) {
            $mhs = Mahasiswa::where('nim', $act['nim'])->first() ?? Mahasiswa::first();
            if (! $mhs) {
                continue;
            }

            KegiatanMahasiswa::firstOrCreate(
                [
                    'mahasiswa_id' => $mhs->id,
                    'nama_kegiatan' => $act['nama_kegiatan'],
                ],
                [
                    'mahasiswa_id' => $mhs->id,
                    'tahun_akademik_id' => $ta2024->id,
                    'nama_kegiatan' => $act['nama_kegiatan'],
                    'jenis_kegiatan' => $act['jenis_kegiatan'],
                    'peran' => $act['peran'],
                    'tingkat' => $act['tingkat'],
                    'tanggal_mulai' => $act['tanggal_mulai'],
                    'tanggal_selesai' => $act['tanggal_selesai'],
                    'penyelenggara' => $act['penyelenggara'],
                    'status_validasi' => $act['status_validasi'],
                ]
            );
        }
    }
}
