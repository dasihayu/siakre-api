<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\KegiatanMahasiswa;
use App\Models\Mahasiswa;
use App\Models\NilaiMahasiswa;
use App\Models\Prodi;
use App\Models\StatusMahasiswa;
use App\Services\AkademikCalculatorService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardPendidikanController extends Controller
{
    /**
     * GET /api/dashboard/pendidikan?id_prodi=&tahun=
     *
     * Mengembalikan KPI dashboard pendidikan:
     * - mahasiswa_aktif_riil  : jumlah mahasiswa berstatus 'Aktif' di prodi & tahun akademik
     * - mahasiswa_baru        : jumlah mahasiswa baru (enrollment pertama) di tahun tersebut
     * - persen_lulus_tepat_waktu : % lulusan dari kohort yang lulus dalam batas jenjang
     * - ipk_rata_rata         : rata-rata IPK tertimbang SKS semua mahasiswa (aktif + lulus) di prodi
     * - rekap_prestasi        : jumlah prestasi per tingkat (lokal, nasional, internasional, dll.)
     */
    public function index(Request $request)
    {
        $request->validate([
            'id_prodi' => 'nullable|exists:prodi,id',
            'tahun' => 'nullable|integer|min:2000|max:2100',
        ]);

        $prodiId = $request->query('id_prodi');
        $tahun = (int) ($request->query('tahun', date('Y')));

        // Pastikan prodi ada jika id_prodi diberikan
        $prodi = $prodiId ? Prodi::findOrFail($prodiId) : null;

        // ── 1. Mahasiswa Aktif Riil ──────────────────────────────────────────
        $mahasiswaAktif = $this->hitungMahasiswaAktif($prodiId, $tahun);

        // ── 2. Mahasiswa Baru ────────────────────────────────────────────────
        $mahasiswaBaru = $this->hitungMahasiswaBaru($prodiId, $tahun);

        // ── 3. % Lulus Tepat Waktu Riil ─────────────────────────────────────
        $dataTepat = $this->hitungPctLulusTepat($prodiId, $prodi);

        // ── 4. IPK Rata-rata Tertimbang SKS ─────────────────────────────────
        $ipkRataRata = $this->hitungIpkRataRata($prodiId);

        // ── 5. Rekap Prestasi per Tingkat ────────────────────────────────────
        $rekapPrestasi = $this->hitungRekapPrestasi($prodiId, $tahun);

        return ApiResponse::success([
            'id_prodi' => $prodiId ? (int) $prodiId : null,
            'nama_prodi' => $prodi?->nama_prodi,
            'tahun' => $tahun,
            'mahasiswa_aktif_riil' => $mahasiswaAktif,
            'mahasiswa_baru' => $mahasiswaBaru,
            'persen_lulus_tepat_waktu' => $dataTepat['persen'],
            'jumlah_lulus_tepat_waktu' => $dataTepat['tepat'],
            'total_lulusan' => $dataTepat['total'],
            'ipk_rata_rata_tertimbang' => $ipkRataRata,
            'rekap_prestasi_per_tingkat' => $rekapPrestasi,
        ], 'Berhasil mengambil ringkasan KPI dashboard pendidikan');
    }

    /**
     * Hitung jumlah mahasiswa yang berstatus 'Aktif' di tahun akademik tertentu.
     */
    private function hitungMahasiswaAktif(?string $prodiId, int $tahun): int
    {
        $query = StatusMahasiswa::where('status', 'Aktif')
            ->whereHas('enrollment', function ($q) use ($prodiId, $tahun) {
                if ($prodiId) {
                    $q->where('prodi_id', $prodiId);
                }

                $q->whereHas('angkatanKelas.tahunAkademik', function ($q2) use ($tahun) {
                    $q2->where('tahun', 'like', "{$tahun}%");
                });
            });

        return $query->count();
    }

    /**
     * Hitung jumlah mahasiswa baru yang mendaftar di suatu tahun.
     * "Baru" = enrollment pertama (tahun_masuk) == $tahun.
     */
    private function hitungMahasiswaBaru(?string $prodiId, int $tahun): int
    {
        $query = Mahasiswa::where('tahun_masuk', $tahun);

        if ($prodiId) {
            $query->whereHas('nilaiMahasiswa.mataKuliahAngkatanKelas.angkatanKelas', function ($q) use ($prodiId) {
                $q->where('prodi_id', $prodiId);
            });
        }

        return $query->count();
    }

    /**
     * Hitung persentase mahasiswa yang lulus tepat waktu sesuai jenjang prodi.
     *
     * @return array{persen: float|null, tepat: int, total: int}
     */
    private function hitungPctLulusTepat(?string $prodiId, ?Prodi $prodi): array
    {
        $batas = $this->batasMasaStudiDariProdi($prodi);

        $queryLulusan = Mahasiswa::whereNotNull('tanggal_kelulusan');

        if ($prodiId) {
            $queryLulusan->whereHas('nilaiMahasiswa.mataKuliahAngkatanKelas.angkatanKelas', function ($q) use ($prodiId) {
                $q->where('prodi_id', $prodiId);
            });
        }

        $semuaLulusan = $queryLulusan->get(['id', 'tahun_masuk', 'tanggal_kelulusan', 'masa_studi_bulan']);

        $total = $semuaLulusan->count();

        if ($total === 0) {
            return ['persen' => null, 'tepat' => 0, 'total' => 0];
        }

        $tepat = $semuaLulusan->filter(function (Mahasiswa $mhs) use ($batas) {
            $masaStudi = $this->hitungMasaStudiBulan($mhs);

            return $masaStudi <= $batas;
        })->count();

        return [
            'persen' => round(($tepat / $total) * 100, 1),
            'tepat' => $tepat,
            'total' => $total,
        ];
    }

    /**
     * Hitung IPK rata-rata tertimbang SKS dari seluruh mahasiswa di prodi.
     */
    private function hitungIpkRataRata(?string $prodiId): ?float
    {
        $nilaiQuery = NilaiMahasiswa::with([
            'mataKuliahAngkatanKelas.enrollmentMataKuliah.mataKuliah:id,sks',
            'mataKuliahAngkatanKelas.angkatanKelas:id,prodi_id',
        ]);

        if ($prodiId) {
            $nilaiQuery->whereHas('mataKuliahAngkatanKelas.angkatanKelas', function ($q) use ($prodiId) {
                $q->where('prodi_id', $prodiId);
            });
        }

        $semuaNilai = $nilaiQuery->get();

        if ($semuaNilai->isEmpty()) {
            return null;
        }

        // Gunakan AkademikCalculatorService dengan rule 'terbaik' di level global
        $result = AkademikCalculatorService::hitungIPK($semuaNilai);

        return $result['total_sks'] > 0 ? $result['ipk'] : null;
    }

    /**
     * Hitung rekap jumlah prestasi mahasiswa per tingkat kegiatan.
     *
     * @return array<string, int>
     */
    private function hitungRekapPrestasi(?string $prodiId, int $tahun): array
    {
        $query = KegiatanMahasiswa::selectRaw('tingkat, COUNT(*) as jumlah')
            ->groupBy('tingkat');

        if ($tahun) {
            $query->whereHas('tahunAkademik', function ($q) use ($tahun) {
                $q->where('tahun', 'like', "{$tahun}%");
            });
        }

        if ($prodiId) {
            $query->whereHas('mahasiswa.nilaiMahasiswa.mataKuliahAngkatanKelas.angkatanKelas', function ($q) use ($prodiId) {
                $q->where('prodi_id', $prodiId);
            });
        }

        return $query->pluck('jumlah', 'tingkat')->toArray();
    }

    /**
     * Hitung masa studi aktual dalam bulan dari data mahasiswa.
     */
    private function hitungMasaStudiBulan(Mahasiswa $mhs): int
    {
        if ($mhs->tahun_masuk && $mhs->tanggal_kelulusan) {
            $mulai = Carbon::create($mhs->tahun_masuk, 8, 1);
            $lulus = Carbon::parse($mhs->tanggal_kelulusan);

            return (int) $mulai->diffInMonths($lulus);
        }

        return (int) ($mhs->masa_studi_bulan ?? 48);
    }

    /**
     * Ambil batas masa studi tepat waktu (bulan) berdasarkan jenjang prodi.
     */
    private function batasMasaStudiDariProdi(?Prodi $prodi): int
    {
        return match ($prodi?->jenjang) {
            'D3' => 36,
            default => 48,
        };
    }
}
