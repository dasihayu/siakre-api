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

        // Angkatan 2018..2026:
        // - 2023..2026: Mahasiswa aktif pada TA 2026/2027 (Tingkat 4..Tingkat 1)
        // - 2018..2022: Mahasiswa yang lulus pada 2022 s/d 2026 dengan variasi kelulusan & drop out
        // Kelas: 0 = A, 1 = B, 2 = C
        // Absen: 01 s.d. 25 (total 75 per angkatan)
        $angkatans = [
            2026 => 26,
            2025 => 25,
            2024 => 24,
            2023 => 23,
            2022 => 22,
            2021 => 21,
            2020 => 20,
            2019 => 19,
            2018 => 18,
        ];

        // Profil kelulusan per angkatan (on_time, late 54 bln, drop out)
        // Menghasilkan persentase kelulusan tepat waktu akurat sesuai standar & tren Figma:
        // 2018 (lulus 2022): 55/75 = 73%
        // 2019 (lulus 2023): 66/75 = 88%
        // 2020 (lulus 2024): 58/75 = 77%
        // 2021 (lulus 2025): 65/75 = 87%
        // 2022 (lulus 2026): 64/75 = 85%
        $cohortProfiles = [
            2018 => ['on_time' => 55, 'late' => 5, 'do' => 15],
            2019 => ['on_time' => 66, 'late' => 4, 'do' => 5],
            2020 => ['on_time' => 58, 'late' => 6, 'do' => 11],
            2021 => ['on_time' => 65, 'late' => 5, 'do' => 5],
            2022 => ['on_time' => 64, 'late' => 4, 'do' => 7],
            2023 => ['on_time' => 0, 'late' => 0, 'do' => 3],
            2024 => ['on_time' => 0, 'late' => 0, 'do' => 2],
            2025 => ['on_time' => 0, 'late' => 0, 'do' => 1],
            2026 => ['on_time' => 0, 'late' => 0, 'do' => 0],
        ];

        $records = [];
        $counter = 0;

        foreach ($angkatans as $tahunMasuk => $aa) {
            $profile = $cohortProfiles[$tahunMasuk] ?? ['on_time' => 60, 'late' => 5, 'do' => 10];

            for ($b = 0; $b <= 2; $b++) {
                for ($cc = 1; $cc <= 25; $cc++) {
                    $studentIndex = ($b * 25) + ($cc - 1); // 0..74
                    $nim = (int) sprintf('433%02d%d%02d', $aa, $b, $cc);

                    if ($tahunMasuk <= 2022) {
                        if ($studentIndex < $profile['on_time']) {
                            // Lulus tepat waktu (48 bulan)
                            $tanggalKelulusan = sprintf('%d-08-25', $tahunMasuk + 4);
                            $masaStudi = 48;
                        } elseif ($studentIndex < ($profile['on_time'] + $profile['late'])) {
                            // Lulus terlambat (54 bulan)
                            $tanggalKelulusan = sprintf('%d-02-25', $tahunMasuk + 5);
                            $masaStudi = 54;
                        } else {
                            // Drop out / tidak lulus
                            $tanggalKelulusan = null;
                            $masaStudi = 0;
                        }
                    } else {
                        // Mahasiswa yang masih aktif menempuh perkuliahan
                        $tanggalKelulusan = null;
                        $masaStudi = 0;
                    }

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
                        'tanggal_kelulusan' => $tanggalKelulusan,
                        'masa_studi_bulan' => $masaStudi,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];

                    $counter++;
                }
            }
        }

        foreach (array_chunk($records, 150) as $chunk) {
            Mahasiswa::upsert(
                $chunk,
                ['nim'],
                ['nama_mahasiswa', 'tahun_masuk', 'jenis_pendaftaran', 'tanggal_kelulusan', 'masa_studi_bulan', 'updated_at']
            );
        }
    }
}
