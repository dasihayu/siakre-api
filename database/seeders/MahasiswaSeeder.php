<?php

namespace Database\Seeders;

use App\Models\Mahasiswa;
use Illuminate\Database\Seeder;

class MahasiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        // Daftar mahasiswa khusus untuk Angkatan 25 Kelas A (4.33.25.0.cc)
        $presetStudents = [
            43325001 => ['nama' => 'Aisa Reta Lestari', 'jalur' => 'SNBP'],
            43325002 => ['nama' => 'Andrian Amirudin Rahmanto', 'jalur' => 'Reguler'],
            43325003 => ['nama' => 'Annisa Nur Farikha', 'jalur' => 'SNBP'],
            43325004 => ['nama' => 'Choirul Raissa Pasha', 'jalur' => 'SNBT'],
            43325006 => ['nama' => 'Dasi Hayu Permana', 'jalur' => 'SNBT'],
            43325007 => ['nama' => 'Dimas Bayu Pratama', 'jalur' => 'Reguler'],
            43325008 => ['nama' => 'Dzidan Adrian Firmansyah', 'jalur' => 'SNBT'],
            43325019 => ['nama' => 'Pranaja Luthfi Belian', 'jalur' => 'Mandiri'],
        ];

        $firstNames = [
            'Aditya', 'Ahmad', 'Aisyah', 'Aldi', 'Alif', 'Amalia', 'Ananda', 'Angga', 'Anisa', 'Arif',
            'Arya', 'Aulia', 'Bagas', 'Bagus', 'Bayu', 'Bima', 'Citra', 'Danang', 'Daffa', 'Dea',
            'Dendy', 'Dewi', 'Dian', 'Dika', 'Dina', 'Dinda', 'Eka', 'Erlangga', 'Fadhil', 'Fahmi',
            'Fajar', 'Farhan', 'Faris', 'Fatimah', 'Fauzan', 'Febri', 'Fikri', 'Galih', 'Gilang', 'Hafiz',
            'Hanif', 'Hasna', 'Hendra', 'Ibrahim', 'Ilham', 'Indah', 'Intan', 'Irfan', 'Ivan', 'Jihan',
            'Kevin', 'Khansa', 'Kurnia', 'Laras', 'Luthfi', 'Mahendra', 'Mawar', 'Meisya', 'Muhammad', 'Nabila',
            'Nadia', 'Naufal', 'Niken', 'Nisa', 'Nova', 'Nurul', 'Pandu', 'Putra', 'Putri', 'Rafi',
            'Rahma', 'Raihan', 'Raka', 'Rama', 'Rangga', 'Ratna', 'Reza', 'Ridho', 'Rifqi', 'Rina',
            'Rizki', 'Rizky', 'Salsa', 'Salma', 'Satria', 'Siti', 'Surya', 'Syifa', 'Taufik', 'Tiara',
            'Teguh', 'Vina', 'Wahyu', 'Widya', 'Wulan', 'Yoga', 'Yuda', 'Yuni', 'Zahra', 'Zaki',
        ];

        $middleNames = [
            'Adi', 'Agung', 'Akbar', 'Aji', 'Aulia', 'Budi', 'Cahya', 'Chandra', 'Dwi', 'Eka',
            'Fajar', 'Firdaus', 'Hadi', 'Indra', 'Jaya', 'Kurnia', 'Kusuma', 'Maulana', 'Mulya', 'Nanda',
            'Nur', 'Pratama', 'Purnama', 'Putra', 'Putri', 'Rahmat', 'Ramdhan', 'Rizki', 'Saputra', 'Saputri',
            'Sari', 'Setia', 'Surya', 'Tri', 'Utama', 'Utami', 'Wahyu', 'Wijaya', 'Yudha', 'Zulkarnain',
        ];

        $lastNames = [
            'Adriansyah', 'Alamsyah', 'Anggraini', 'Ardiansyah', 'Astuti', 'Budiman', 'Cahyani', 'Cahyono', 'Damayanti', 'Darojat',
            'Febrianto', 'Firmansyah', 'Gunawan', 'Hakim', 'Halim', 'Handayani', 'Hardiansyah', 'Hartono', 'Hermawan', 'Hidayat',
            'Hidayatullah', 'Irawan', 'Kurniawan', 'Kurniawati', 'Kusuma', 'Laksono', 'Lestari', 'Mahardika', 'Mahendra', 'Mulyani',
            'Mulyono', 'Nugraha', 'Nugroho', 'Nurcahyo', 'Oktaviani', 'Pamungkas', 'Permana', 'Pradana', 'Pradipta', 'Prakoso',
            'Prameswari', 'Pranata', 'Prasetiyo', 'Prasetya', 'Prasetyo', 'Pratama', 'Prayoga', 'Purnomo', 'Purwanto', 'Puspitasari',
            'Putra', 'Putri', 'Raharjo', 'Rahmadani', 'Rahmawati', 'Ramadhan', 'Riyadi', 'Rosyid', 'Santoso', 'Saputra',
            'Saputro', 'Sari', 'Setiawan', 'Setyawan', 'Siregar', 'Subagyo', 'Suhendra', 'Sulistyo', 'Supriyanto', 'Suryani',
            'Susanto', 'Susilo', 'Syahputra', 'Utomo', 'Wardani', 'Wibisono', 'Wibowo', 'Wicaksono', 'Widodo', 'Wijaya',
            'Wijayanti', 'Wulandari', 'Yulianto', 'Yuliana', 'Zulkarnain',
        ];

        $jalurOptions = ['SNBP', 'SNBT', 'Mandiri', 'Reguler'];

        // Angkatan: 2026 (26), 2025 (25), 2024 (24), 2023 (23)
        // Kelas: 0 = A, 1 = B, 2 = C
        // Absen: 01 s.d. 25
        $angkatans = [
            2026 => 26,
            2025 => 25,
            2024 => 24,
            2023 => 23,
        ];

        $records = [];
        $counter = 0;

        foreach ($angkatans as $tahunMasuk => $aa) {
            for ($b = 0; $b <= 2; $b++) {
                for ($cc = 1; $cc <= 25; $cc++) {
                    $nim = (int) sprintf('433%02d%d%02d', $aa, $b, $cc);

                    if (isset($presetStudents[$nim])) {
                        $nama = $presetStudents[$nim]['nama'];
                        $jalur = $presetStudents[$nim]['jalur'];
                    } else {
                        $fn = $firstNames[($counter * 7 + $cc) % count($firstNames)];
                        $mn = $middleNames[($counter * 13 + $b * 5 + $cc) % count($middleNames)];
                        $ln = $lastNames[($counter * 19 + $aa + $cc) % count($lastNames)];
                        $nama = "{$fn} {$mn} {$ln}";
                        $jalur = $jalurOptions[$counter % count($jalurOptions)];
                    }

                    $records[] = [
                        'nim' => $nim,
                        'nama_mahasiswa' => $nama,
                        'tahun_masuk' => $tahunMasuk,
                        'jenis_pendaftaran' => $jalur,
                        'tanggal_kelulusan' => null,
                        'masa_studi_bulan' => 48,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];

                    $counter++;
                }
            }
        }

        foreach (array_chunk($records, 100) as $chunk) {
            Mahasiswa::upsert(
                $chunk,
                ['nim'],
                ['nama_mahasiswa', 'tahun_masuk', 'jenis_pendaftaran', 'tanggal_kelulusan', 'masa_studi_bulan', 'updated_at']
            );
        }
    }
}
