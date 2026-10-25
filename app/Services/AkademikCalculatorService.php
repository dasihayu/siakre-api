<?php

namespace App\Services;

use App\Models\NilaiMahasiswa;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class AkademikCalculatorService
{
    /**
     * Konversi nilai huruf menjadi bobot angka sesuai standar LAM-INFOKOM / Kampus.
     */
    public static function bobotNilai(string $nilai): float
    {
        $konversi = [
            'A' => 4.0,
            'AB' => 3.5,
            'B' => 3.0,
            'BC' => 2.5,
            'C' => 2.0,
            'D' => 1.0,
            'E' => 0.0,
        ];

        $nilaiClean = strtoupper(trim($nilai));

        if (! array_key_exists($nilaiClean, $konversi)) {
            throw new InvalidArgumentException("Nilai huruf tidak valid: {$nilai}");
        }

        return $konversi[$nilaiClean];
    }

    /**
     * Menghitung IP (Indeks Prestasi) semester dari daftar mata kuliah / nilai.
     *
     * Rumus: IP = SUM(SKS_i * Bobot_i) / SUM(SKS_i)
     *
     * @param  array<int, mixed>|Collection  $items
     * @return array{total_sks: int, total_kredit: float, ip: float}
     */
    public static function hitungIP(array|Collection $items): array
    {
        $totalSKS = 0;
        $totalKredit = 0.0;

        foreach ($items as $item) {
            $sks = 0;
            $bobot = 0.0;

            if ($item instanceof NilaiMahasiswa) {
                $mk = $item->mataKuliahAngkatanKelas?->enrollmentMataKuliah?->mataKuliah;
                $sks = (int) ($mk?->sks ?? 0);
                $bobot = (float) ($item->bobot ?? (isset($item->nilai_huruf) ? self::bobotNilai($item->nilai_huruf) : 0.0));
            } elseif (is_array($item)) {
                $sks = (int) ($item['sks'] ?? 0);
                if (isset($item['bobot'])) {
                    $bobot = (float) $item['bobot'];
                } elseif (isset($item['nilai']) || isset($item['nilai_huruf'])) {
                    $huruf = (string) ($item['nilai'] ?? $item['nilai_huruf']);
                    $bobot = self::bobotNilai($huruf);
                }
            } elseif (is_object($item)) {
                $sks = (int) ($item->sks ?? 0);
                if (isset($item->bobot)) {
                    $bobot = (float) $item->bobot;
                } elseif (isset($item->nilai) || isset($item->nilai_huruf)) {
                    $huruf = (string) ($item->nilai ?? $item->nilai_huruf);
                    $bobot = self::bobotNilai($huruf);
                }
            }

            if ($sks <= 0) {
                continue;
            }

            $totalSKS += $sks;
            $totalKredit += ($sks * $bobot);
        }

        $ip = $totalSKS > 0 ? round($totalKredit / $totalSKS, 2) : 0.0;

        return [
            'total_sks' => $totalSKS,
            'total_kredit' => round($totalKredit, 2),
            'ip' => $ip,
        ];
    }

    /**
     * Menghitung IPK (Indeks Prestasi Kumulatif) dengan penanganan mata kuliah mengulang.
     *
     * Aturan Mata Kuliah Mengulang ($ruleMengulang):
     * - 'terbaik' (default): Jika suatu mata kuliah diambil lebih dari sekali, nilai dengan bobot tertinggi yang dihitung.
     * - 'terbaru': Nilai pada semester paling akhir yang dihitung.
     * - 'semua': Seluruh pengambilan dihitung.
     *
     * @param  array<int, mixed>|Collection  $items
     * @return array{total_sks: int, total_kredit: float, ipk: float, items: array<int, array<string, mixed>>}
     */
    public static function hitungIPK(array|Collection $items, string $ruleMengulang = 'terbaik'): array
    {
        $normalized = [];

        foreach ($items as $idx => $item) {
            $id = $idx;
            $kodeMk = '';
            $namaMk = '';
            $sks = 0;
            $bobot = 0.0;
            $nilaiHuruf = '';
            $nilaiAngka = null;
            $tahunAkademik = '';
            $semester = '';
            $linkNilai = null;
            $linkEvidence = null;

            if ($item instanceof NilaiMahasiswa) {
                $id = $item->id;
                $mk = $item->mataKuliahAngkatanKelas?->enrollmentMataKuliah?->mataKuliah;
                $kodeMk = $mk?->kode_mata_kuliah ?? "MK-{$idx}";
                $namaMk = $mk?->nama_mata_kuliah ?? '';
                $sks = (int) ($mk?->sks ?? 0);
                $bobot = (float) ($item->bobot ?? 0.0);
                $nilaiHuruf = (string) ($item->nilai_huruf ?? '');
                $nilaiAngka = $item->nilai_angka;
                $tahunAkademik = $item->tahunAkademik?->tahun ?? '';
                $semester = $item->semester ?? '';
                $linkNilai = $item->link_nilai;
                $linkEvidence = $item->link_evidence;
            } elseif (is_array($item)) {
                $id = $item['id'] ?? $idx;
                $kodeMk = (string) ($item['kode_mata_kuliah'] ?? $item['kode'] ?? "MK-{$idx}");
                $namaMk = (string) ($item['nama_mata_kuliah'] ?? $item['nama'] ?? '');
                $sks = (int) ($item['sks'] ?? 0);
                $bobot = isset($item['bobot']) ? (float) $item['bobot'] : (isset($item['nilai']) ? self::bobotNilai($item['nilai']) : 0.0);
                $nilaiHuruf = (string) ($item['nilai_huruf'] ?? $item['nilai'] ?? '');
                $nilaiAngka = $item['nilai_angka'] ?? null;
                $tahunAkademik = $item['tahun_akademik'] ?? '';
                $semester = $item['semester'] ?? '';
                $linkNilai = $item['link_nilai'] ?? null;
                $linkEvidence = $item['link_evidence'] ?? null;
            }

            $kredit = round($sks * $bobot, 2);

            $normalized[] = [
                'id' => $id,
                'kode_mata_kuliah' => $kodeMk,
                'nama_mata_kuliah' => $namaMk,
                'sks' => $sks,
                'nilai_angka' => $nilaiAngka,
                'nilai_huruf' => $nilaiHuruf,
                'bobot' => $bobot,
                'kredit' => $kredit,
                'tahun_akademik' => $tahunAkademik,
                'semester' => $semester,
                'link_nilai' => $linkNilai,
                'link_evidence' => $linkEvidence,
                'is_diperhitungkan_ipk' => true,
                'keterangan' => null,
            ];
        }

        // Evaluasi mata kuliah mengulang
        if ($ruleMengulang !== 'semua') {
            $groupedByCourse = [];
            foreach ($normalized as $index => $row) {
                $code = $row['kode_mata_kuliah'];
                $groupedByCourse[$code][] = $index;
            }

            foreach ($groupedByCourse as $code => $indices) {
                if (count($indices) > 1) {
                    if ($ruleMengulang === 'terbaik') {
                        $bestIndex = $indices[0];
                        foreach ($indices as $idx) {
                            $curr = $normalized[$idx];
                            $best = $normalized[$bestIndex];

                            if ($curr['bobot'] > $best['bobot'] ||
                                ($curr['bobot'] === $best['bobot'] && ($curr['nilai_angka'] ?? 0) > ($best['nilai_angka'] ?? 0))) {
                                $bestIndex = $idx;
                            }
                        }

                        foreach ($indices as $idx) {
                            if ($idx !== $bestIndex) {
                                $normalized[$idx]['is_diperhitungkan_ipk'] = false;
                                $normalized[$idx]['keterangan'] = 'Mata kuliah diulang (diambil nilai tertinggi)';
                            }
                        }
                    } elseif ($ruleMengulang === 'terbaru') {
                        $lastIndex = end($indices);
                        foreach ($indices as $idx) {
                            if ($idx !== $lastIndex) {
                                $normalized[$idx]['is_diperhitungkan_ipk'] = false;
                                $normalized[$idx]['keterangan'] = 'Mata kuliah diulang (diambil nilai terbaru)';
                            }
                        }
                    }
                }
            }
        }

        $totalSKS = 0;
        $totalKredit = 0.0;

        foreach ($normalized as $row) {
            if ($row['is_diperhitungkan_ipk'] && $row['sks'] > 0) {
                $totalSKS += $row['sks'];
                $totalKredit += $row['kredit'];
            }
        }

        $ipk = $totalSKS > 0 ? round($totalKredit / $totalSKS, 2) : 0.0;

        return [
            'total_sks' => $totalSKS,
            'total_kredit' => round($totalKredit, 2),
            'ipk' => $ipk,
            'items' => $normalized,
        ];
    }
}
