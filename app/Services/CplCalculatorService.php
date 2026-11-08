<?php

namespace App\Services;

use App\Models\Cpl;
use Illuminate\Support\Facades\DB;

class CplCalculatorService
{
    /**
     * @return array{
     *     id_kurikulum: int,
     *     angkatan: ?int,
     *     target: float,
     *     cpl: array<int, array<string, mixed>>
     * }
     */
    public function calculate(int $idKurikulum, ?int $angkatan, float $target = 70): array
    {
        $scores = DB::table('nilai_mahasiswa as nilai')
            ->join('mata_kuliah_angkatan_kelas as mkak', 'mkak.id', '=', 'nilai.mata_kuliah_angkatan_kelas_id')
            ->join('enrollment_mata_kuliah as emk', 'emk.id', '=', 'mkak.enrollment_mata_kuliah_id')
            ->join('mata_kuliah as mk', 'mk.id', '=', 'emk.mata_kuliah_id')
            ->join('mata_kuliah_cpl as mapping', 'mapping.id_mata_kuliah', '=', 'mk.id')
            ->where('mk.id_kurikulum', $idKurikulum)
            ->where('mapping.bobot', '>', 0)
            ->whereNotNull('nilai.nilai_angka')
            ->when($angkatan !== null, fn ($query) => $query->whereExists(
                fn ($subquery) => $subquery
                    ->selectRaw('1')
                    ->from('data_mahasiswa')
                    ->whereColumn('data_mahasiswa.id', 'nilai.mahasiswa_id')
                    ->where('data_mahasiswa.tahun_masuk', $angkatan)
            ))
            ->selectRaw(
                'mapping.id_cpl, SUM(nilai.nilai_angka * mapping.bobot) / NULLIF(SUM(mapping.bobot), 0) as skor_aktual, COUNT(DISTINCT nilai.mahasiswa_id) as jumlah_mahasiswa, COUNT(nilai.id) as jumlah_penilaian'
            )
            ->groupBy('mapping.id_cpl')
            ->get()
            ->keyBy('id_cpl');

        $cpl = Cpl::query()
            ->where('id_kurikulum', $idKurikulum)
            ->orderBy('kode_cpl')
            ->get()
            ->map(function (Cpl $item) use ($scores, $target): array {
                $score = $scores->get($item->id);
                $actual = $score !== null ? round((float) $score->skor_aktual, 2) : null;

                return [
                    'id' => $item->id,
                    'kode_cpl' => $item->kode_cpl,
                    'deskripsi_cpl' => $item->deskripsi_cpl,
                    'skor_aktual' => $actual,
                    'target' => $target,
                    'status' => $actual !== null && $actual >= $target ? 'Tercapai' : 'Belum Tercapai',
                    'jumlah_mahasiswa' => (int) ($score->jumlah_mahasiswa ?? 0),
                    'jumlah_penilaian' => (int) ($score->jumlah_penilaian ?? 0),
                ];
            })
            ->all();

        return [
            'id_kurikulum' => $idKurikulum,
            'angkatan' => $angkatan,
            'target' => $target,
            'cpl' => $cpl,
        ];
    }
}
