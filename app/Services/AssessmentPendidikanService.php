<?php

namespace App\Services;

use App\Models\Angkatan;
use App\Models\Cpl;
use App\Models\Mahasiswa;
use App\Models\NilaiMahasiswa;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class AssessmentPendidikanService
{
    public function __construct(private readonly CplCalculatorService $cplCalculatorService) {}

    /**
     * @return array{
     *     filters: array{id_kurikulum: ?int, angkatan: ?int},
     *     criteria: array<int, array<string, mixed>>
     * }
     */
    public function gap(?int $idKurikulum = null, ?int $angkatan = null): array
    {
        $targets = config('assessment.targets');
        $cplScores = $this->cplScores($idKurikulum, $angkatan);
        $graduation = $this->onTimeGraduationRate($angkatan);
        $averageGpa = $this->averageGpa($angkatan);
        $admissionRatio = $this->newStudentApplicantRatio($angkatan);

        return [
            'filters' => [
                'id_kurikulum' => $idKurikulum,
                'angkatan' => $angkatan,
            ],
            'criteria' => [
                $this->metric(
                    'ketercapaian_cpl',
                    'Ketercapaian CPL',
                    $cplScores['score'],
                    (float) $targets['cpl'],
                    '%',
                    'Tinjau pemetaan CPL dan perkuat asesmen serta pembelajaran pada mata kuliah terkait.',
                    $cplScores['count']
                ),
                $this->metric(
                    'kelulusan_tepat_waktu',
                    'Kelulusan Tepat Waktu',
                    $graduation['score'],
                    (float) $targets['kelulusan_tepat_waktu'],
                    '%',
                    'Evaluasi hambatan studi, pemenuhan prasyarat, dan tindak lanjut akademik mahasiswa.',
                    $graduation['count']
                ),
                $this->metric(
                    'rata_rata_ipk',
                    'Rata-rata IPK',
                    $averageGpa['score'],
                    (float) $targets['ipk_rata_rata'],
                    'skala 4',
                    'Perkuat pembimbingan akademik, remediasi, dan dukungan belajar pada mata kuliah berisiko.',
                    $averageGpa['count']
                ),
                $this->metric(
                    'rasio_mahasiswa_baru_pendaftar',
                    'Rasio Mahasiswa Baru terhadap Pendaftar',
                    $admissionRatio['score'],
                    (float) $targets['rasio_mahasiswa_baru_pendaftar'],
                    '%',
                    'Tingkatkan jangkauan promosi dan evaluasi efektivitas jalur penerimaan mahasiswa.',
                    $admissionRatio['count']
                ),
            ],
        ];
    }

    /**
     * @return array{score: ?float, count: int}
     */
    private function cplScores(?int $idKurikulum, ?int $angkatan): array
    {
        $curriculumIds = $idKurikulum === null
            ? Cpl::query()->distinct()->orderBy('id_kurikulum')->pluck('id_kurikulum')
            : collect([$idKurikulum]);

        $scores = $curriculumIds
            ->flatMap(fn (int $curriculumId): array => $this->cplCalculatorService
                ->calculate($curriculumId, $angkatan, (float) config('assessment.targets.cpl'))['cpl'])
            ->pluck('skor_aktual')
            ->filter(fn (?float $score): bool => $score !== null);

        return [
            'score' => $scores->isNotEmpty() ? round((float) $scores->avg(), 2) : null,
            'count' => $scores->count(),
        ];
    }

    /**
     * @return array{score: ?float, count: int}
     */
    private function onTimeGraduationRate(?int $angkatan): array
    {
        $graduates = Mahasiswa::query()
            ->whereNotNull('tanggal_kelulusan')
            ->when($angkatan !== null, fn (Builder $query) => $query->where('tahun_masuk', $angkatan));

        $graduateCount = (clone $graduates)->count();
        $onTimeCount = (clone $graduates)
            ->where('masa_studi_bulan', '<=', (int) config('assessment.on_time_months'))
            ->count();

        return [
            'score' => $graduateCount > 0 ? round(($onTimeCount / $graduateCount) * 100, 2) : null,
            'count' => $graduateCount,
        ];
    }

    /**
     * @return array{score: ?float, count: int}
     */
    private function averageGpa(?int $angkatan): array
    {
        $grades = NilaiMahasiswa::query()
            ->where(function (Builder $query): void {
                $query->whereNotNull('nilai_angka')->orWhereNotNull('nilai_huruf');
            })
            ->when(
                $angkatan !== null,
                fn (Builder $query) => $query->whereHas(
                    'mahasiswa',
                    fn (Builder $studentQuery) => $studentQuery->where('tahun_masuk', $angkatan)
                )
            )
            ->with(['mataKuliahAngkatanKelas.enrollmentMataKuliah.mataKuliah', 'tahunAkademik'])
            ->get()
            ->groupBy('mahasiswa_id');

        $gpas = $grades
            ->map(function (Collection $studentGrades): ?float {
                $courses = $studentGrades
                    ->map(function (NilaiMahasiswa $grade): array {
                        $mataKuliah = $grade->mataKuliahAngkatanKelas?->enrollmentMataKuliah?->mataKuliah;
                        $gradeWeight = $grade->bobot;

                        if ($gradeWeight === null && $grade->nilai_huruf !== null) {
                            $gradeWeight = AkademikCalculatorService::bobotNilai($grade->nilai_huruf);
                        }

                        return [
                            'kode_mata_kuliah' => $mataKuliah?->kode_mata_kuliah ?? "MK-{$grade->id}",
                            'sks' => (int) ($mataKuliah?->sks ?? 0),
                            'bobot' => (float) ($gradeWeight ?? 0),
                            'nilai_huruf' => $grade->nilai_huruf,
                            'nilai_angka' => $grade->nilai_angka,
                            'tahun_akademik' => $grade->tahunAkademik?->tahun ?? '',
                            'semester' => $grade->semester ?? '',
                        ];
                    });

                $result = AkademikCalculatorService::hitungIPK($courses);

                return $result['total_sks'] > 0 ? (float) $result['ipk'] : null;
            })
            ->filter(fn (?float $gpa): bool => $gpa !== null);

        return [
            'score' => $gpas->isNotEmpty() ? round((float) $gpas->avg(), 2) : null,
            'count' => $gpas->count(),
        ];
    }

    /**
     * @return array{score: ?float, count: int}
     */
    private function newStudentApplicantRatio(?int $angkatan): array
    {
        $cohorts = Angkatan::query()
            ->whereNotNull('jumlah_pendaftar')
            ->where('jumlah_pendaftar', '>', 0)
            ->when($angkatan !== null, fn (Builder $query) => $query->where('angkatan', $angkatan))
            ->get(['angkatan', 'jumlah_pendaftar']);

        if ($cohorts->isEmpty()) {
            return ['score' => null, 'count' => 0];
        }

        $studentCounts = Mahasiswa::query()
            ->whereIn('tahun_masuk', $cohorts->pluck('angkatan'))
            ->selectRaw('tahun_masuk, COUNT(*) as jumlah')
            ->groupBy('tahun_masuk')
            ->pluck('jumlah', 'tahun_masuk');

        $applicantCount = (int) $cohorts->sum('jumlah_pendaftar');
        $newStudentCount = (int) $cohorts->sum(
            fn (Angkatan $cohort): int => (int) ($studentCounts->get($cohort->angkatan) ?? 0)
        );

        return [
            'score' => $applicantCount > 0 ? round(($newStudentCount / $applicantCount) * 100, 2) : null,
            'count' => $applicantCount,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function metric(
        string $code,
        string $name,
        ?float $actual,
        float $target,
        string $unit,
        string $recommendation,
        int $sampleCount
    ): array {
        $gap = $actual !== null ? round($actual - $target, 2) : null;
        $status = $gap === null ? 'Data Tidak Tersedia' : ($gap >= 0 ? 'Tercapai' : 'Belum Tercapai');

        if ($gap === null) {
            $recommendation = match ($code) {
                'ketercapaian_cpl' => 'Lengkapi nilai mahasiswa, pemetaan mata kuliah-CPL, dan filter kurikulum/angkatan.',
                'kelulusan_tepat_waktu' => 'Lengkapi data tanggal kelulusan dan masa studi mahasiswa.',
                'rata_rata_ipk' => 'Lengkapi data nilai numerik atau nilai huruf mahasiswa.',
                'rasio_mahasiswa_baru_pendaftar' => 'Isi jumlah pendaftar pada data angkatan dan lengkapi data mahasiswa baru.',
                default => $recommendation,
            };
        } elseif ($gap >= 0) {
            $recommendation = "Pertahankan capaian {$name} dan lakukan pemantauan berkala.";
        }

        return [
            'kode' => $code,
            'nama' => $name,
            'skor_aktual' => $actual,
            'target' => $target,
            'gap' => $gap,
            'satuan' => $unit,
            'status' => $status,
            'jumlah_data' => $sampleCount,
            'rekomendasi' => $recommendation,
        ];
    }
}
