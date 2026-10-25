<?php

namespace Database\Seeders;

use App\Models\EnrollmentKelas;
use App\Models\MataKuliahAngkatanKelas;
use App\Models\NilaiMahasiswa;
use Illuminate\Database\Seeder;

class NilaiMahasiswaSeeder extends Seeder
{
    /**
     * Semester kurikulum maksimal yang SUDAH memiliki nilai untuk setiap angkatan
     * pada posisi saat ini (Tahun Akademik aktif: 2026/2027 Ganjil):
     * - Angkatan 2026 (Tingkat 1 Semester 1): 0 (belum ada nilai)
     * - Angkatan 2025 (Tingkat 2 Semester 1): 2 (hanya Tingkat 1 Sem 1 & Tingkat 1 Sem 2)
     * - Angkatan 2024 (Tingkat 3 Semester 1): 4 (Tingkat 1 Sem 1-2 & Tingkat 2 Sem 1-2)
     * - Angkatan 2023 (Tingkat 4 Semester 1): 6 (Tingkat 1 Sem 1-2, Tingkat 2 Sem 1-2, Tingkat 3 Sem 1-2)
     *
     * @return array<int, int>
     */
    public static function getMaxGradedSemesterByAngkatan(): array
    {
        return [
            2026 => 0,
            2025 => 2,
            2024 => 4,
            2023 => 6,
        ];
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courses = MataKuliahAngkatanKelas::with([
            'enrollmentMataKuliah.mataKuliah',
            'enrollmentMataKuliah.tahunAkademik',
            'angkatanKelas.angkatan',
        ])->get();

        $enrollmentsByAk = EnrollmentKelas::all()->groupBy('angkatan_kelas_id');

        if ($courses->isEmpty() || $enrollmentsByAk->isEmpty()) {
            return;
        }

        $maxGradedSemByAngkatan = self::getMaxGradedSemesterByAngkatan();
        $now = now();
        $records = [];

        foreach ($courses as $course) {
            $mk = $course->enrollmentMataKuliah?->mataKuliah;
            $ta = $course->enrollmentMataKuliah?->tahunAkademik;
            $angkatan = (int) ($course->angkatanKelas?->angkatan?->angkatan ?? 0);

            if (! $mk || ! $ta || ! isset($maxGradedSemByAngkatan[$angkatan])) {
                continue;
            }

            $sem = EnrollmentMataKuliahSeeder::extractSemesterFromKode($mk->kode_mata_kuliah);
            $maxGradedSem = $maxGradedSemByAngkatan[$angkatan];

            // Hanya buat nilai untuk semester yang sudah selesai ditempuh
            if ($sem > $maxGradedSem) {
                continue;
            }

            $studentEnrollments = $enrollmentsByAk->get($course->angkatan_kelas_id, collect());
            foreach ($studentEnrollments as $enrollment) {
                $studentId = $enrollment->id_mahasiswa;

                // Menghasilkan nilai angka realistis yang deterministik-variatif antara 68.00 - 94.50
                $seed = crc32("{$course->id}_{$studentId}_{$mk->kode_mata_kuliah}");
                $nilaiAngka = round(68 + (($seed % 2650) / 100), 2);
                $calc = NilaiMahasiswa::hitungNilaiHurufDanBobot($nilaiAngka);

                $records[] = [
                    'mata_kuliah_angkatan_kelas_id' => $course->id,
                    'mahasiswa_id' => $studentId,
                    'tahun_akademik_id' => $ta->id,
                    'nilai_angka' => $nilaiAngka,
                    'nilai_huruf' => $calc['nilai_huruf'],
                    'bobot' => $calc['bobot'],
                    'link_nilai' => null,
                    'link_evidence' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        foreach (array_chunk($records, 500) as $chunk) {
            NilaiMahasiswa::upsert(
                $chunk,
                ['mata_kuliah_angkatan_kelas_id', 'mahasiswa_id'],
                ['tahun_akademik_id', 'nilai_angka', 'nilai_huruf', 'bobot', 'updated_at']
            );
        }
    }
}
