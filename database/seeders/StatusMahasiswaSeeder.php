<?php

namespace Database\Seeders;

use App\Models\EnrollmentKelas;
use App\Models\StatusMahasiswa;
use Illuminate\Database\Seeder;

class StatusMahasiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $enrollments = EnrollmentKelas::with([
            'mahasiswa',
            'angkatanKelas.tahunAkademik',
        ])->get();

        if ($enrollments->isEmpty()) {
            return;
        }

        StatusMahasiswa::query()->delete();

        $now = now();
        $records = [];

        $presetNims = [43325001, 43325002, 43325003, 43325004, 43325006, 43325007, 43325008, 43325019];

        foreach ($enrollments as $enrollment) {
            $mhs = $enrollment->mahasiswa;
            $ta = $enrollment->angkatanKelas?->tahunAkademik;
            $startYear = (int) substr((string) ($ta?->tahun ?? '2026/2027'), 0, 4);
            $semType = $ta?->semester ?? 'Ganjil';
            $tahunMasuk = (int) ($mhs?->tahun_masuk ?? 2026);
            $nim = (int) ($mhs?->nim ?? 0);

            // Hitung nomor semester berjalan relatif terhadap tahun masuk (1..8+)
            $semesterKe = (($startYear - $tahunMasuk) * 2) + ($semType === 'Genap' ? 2 : 1);

            $status = 'Aktif';

            if (! in_array($nim, $presetNims, true)) {
                if ($mhs?->tanggal_kelulusan) {
                    $masaStudi = (int) ($mhs->masa_studi_bulan ?? 48);
                    $gradSemester = $masaStudi <= 48 ? 8 : 9;

                    if ($semesterKe >= $gradSemester) {
                        $status = 'Lulus';
                    } elseif ($mhs->nim % 37 === 7 && in_array($semesterKe, [4, 5], true)) {
                        $status = 'Cuti';
                    } elseif ($mhs->nim % 43 === 11 && in_array($semesterKe, [3, 6], true)) {
                        $status = 'Non-Aktif';
                    }
                } else {
                    // Mahasiswa tanpa tanggal kelulusan
                    $isDo = ($tahunMasuk <= 2022 && (int) ($mhs?->masa_studi_bulan ?? 0) === 0)
                        || ($tahunMasuk >= 2023 && ($nim % 75 >= 72)); // beberapa mahasiswa DO di angkatan aktif

                    if ($isDo) {
                        $doSemester = 3 + ($nim % 3); // DO mulai semester 3, 4, atau 5
                        if ($semesterKe >= $doSemester) {
                            $status = 'Drop Out';
                        }
                    } else {
                        // Mahasiswa aktif yang menempuh kuliah normal
                        if ($mhs->nim % 29 === 5 && in_array($semesterKe, [3, 5], true)) {
                            $status = 'Cuti';
                        } elseif ($mhs->nim % 41 === 9 && in_array($semesterKe, [4, 6], true)) {
                            $status = 'Non-Aktif';
                        }
                    }
                }
            } else {
                // Preset students selalu Aktif
                $status = 'Aktif';
            }

            $records[] = [
                'id_enrollment_mahasiswa_angkatan_kelas' => $enrollment->id,
                'status' => $status,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach (array_chunk($records, 500) as $chunk) {
            StatusMahasiswa::insert($chunk);
        }
    }
}
