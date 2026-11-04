<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\EnrollmentKelas;
use App\Models\Mahasiswa;
use App\Services\AkademikCalculatorService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LulusanController extends Controller
{
    /**
     * GET /api/lulusan
     *
     * Mengembalikan daftar mahasiswa yang sudah lulus (tanggal_kelulusan terisi),
     * dilengkapi masa studi aktual (bulan), IPK riil, dan status kelulusan tepat waktu
     * yang adaptif sesuai jenjang prodi (D3 = 36 bulan, D4/S1 = 48 bulan).
     *
     * Query params: id_prodi, angkatan, tahun_lulus, per_page
     */
    public function index(Request $request)
    {
        $query = Mahasiswa::query()
            ->whereNotNull('tanggal_kelulusan')
            ->with([
                'nilaiMahasiswa.mataKuliahAngkatanKelas.enrollmentMataKuliah.mataKuliah',
                'nilaiMahasiswa.tahunAkademik',
            ]);

        // Filter opsional berdasarkan prodi via enrollment
        if ($request->filled('id_prodi')) {
            $prodiId = $request->query('id_prodi');
            $query->whereHas('nilaiMahasiswa.mataKuliahAngkatanKelas.angkatanKelas', function ($q) use ($prodiId) {
                $q->where('prodi_id', $prodiId);
            });
        }

        if ($request->filled('angkatan')) {
            $query->where('tahun_masuk', $request->query('angkatan'));
        }

        if ($request->filled('tahun_lulus')) {
            $query->whereYear('tanggal_kelulusan', $request->query('tahun_lulus'));
        }

        $perPage = (int) $request->query('per_page', 25);
        $lulusan = $query->paginate($perPage);

        $result = $lulusan->through(function (Mahasiswa $mhs) use ($request) {
            return $this->formatLulusan($mhs, $request->query('id_prodi'));
        });

        return ApiResponse::success($result, 'Berhasil mengambil data lulusan');
    }

    /**
     * GET /api/lulusan/ringkasan
     *
     * Mengembalikan statistik agregat lulusan: total, rata-rata masa studi,
     * rata-rata IPK, dan % lulus tepat waktu.
     *
     * Query params: id_prodi, angkatan, tahun_lulus
     */
    public function ringkasan(Request $request)
    {
        $query = Mahasiswa::query()->whereNotNull('tanggal_kelulusan');

        if ($request->filled('angkatan')) {
            $query->where('tahun_masuk', $request->query('angkatan'));
        }

        if ($request->filled('tahun_lulus')) {
            $query->whereYear('tanggal_kelulusan', $request->query('tahun_lulus'));
        }

        // Filter prodi melalui enrollment
        if ($request->filled('id_prodi')) {
            $prodiId = $request->query('id_prodi');
            $query->whereHas('nilaiMahasiswa.mataKuliahAngkatanKelas.angkatanKelas', function ($q) use ($prodiId) {
                $q->where('prodi_id', $prodiId);
            });
        }

        $semuaLulusan = $query->with([
            'nilaiMahasiswa.mataKuliahAngkatanKelas.enrollmentMataKuliah.mataKuliah',
            'nilaiMahasiswa.tahunAkademik',
        ])->get();

        $prodiId = $request->query('id_prodi');
        $formatted = $semuaLulusan->map(fn ($mhs) => $this->formatLulusan($mhs, $prodiId));

        $total = $formatted->count();
        $tepat = $formatted->filter(fn ($d) => $d['lulus_tepat_waktu'])->count();

        $rataIpk = $total > 0 ? round($formatted->avg('ipk_riil'), 2) : null;
        $rataMasaStudi = $total > 0 ? round($formatted->avg('masa_studi_bulan'), 1) : null;
        $pctTepat = $total > 0 ? round(($tepat / $total) * 100, 1) : null;

        return ApiResponse::success([
            'total_lulusan' => $total,
            'rata_rata_masa_studi_bulan' => $rataMasaStudi,
            'rata_rata_ipk' => $rataIpk,
            'persen_lulus_tepat_waktu' => $pctTepat,
            'jumlah_lulus_tepat_waktu' => $tepat,
        ], 'Berhasil mengambil ringkasan data lulusan');
    }

    /**
     * Hitung dan format data lulusan untuk satu mahasiswa.
     *
     * @param  int|string|null  $prodiId
     * @return array<string, mixed>
     */
    private function formatLulusan(Mahasiswa $mhs, mixed $prodiId = null): array
    {
        // ── Kalkulasi masa studi aktual ───────────────────────────────────────
        $masaStudiBulan = $mhs->masa_studi_bulan;

        if ($mhs->tahun_masuk && $mhs->tanggal_kelulusan) {
            $mulai = Carbon::create($mhs->tahun_masuk, 8, 1); // asumsi mulai Agustus
            $lulus = Carbon::parse($mhs->tanggal_kelulusan);
            $masaStudiBulan = (int) $mulai->diffInMonths($lulus);
        }

        // ── Jenjang prodi & batas tepat waktu ────────────────────────────────
        $jenjang = $this->ambilJenjangMahasiswa($mhs, $prodiId);
        $batasTepat = $this->batasMasaStudiTepat($jenjang);

        // ── IPK riil via AkademikCalculatorService ────────────────────────────
        $nilaiList = $mhs->nilaiMahasiswa ?? collect();
        $ipkResult = AkademikCalculatorService::hitungIPK($nilaiList);

        return [
            'id' => $mhs->id,
            'nim' => $mhs->nim,
            'nim_display' => $mhs->nim_display,
            'nama_mahasiswa' => $mhs->nama_mahasiswa,
            'tahun_masuk' => $mhs->tahun_masuk,
            'jenis_pendaftaran' => $mhs->jenis_pendaftaran,
            'tanggal_kelulusan' => $mhs->tanggal_kelulusan,
            'jenjang_prodi' => $jenjang,
            'batas_masa_studi_bulan' => $batasTepat,
            'masa_studi_bulan' => $masaStudiBulan,
            'ipk_riil' => $ipkResult['ipk'],
            'total_sks' => $ipkResult['total_sks'],
            'lulus_tepat_waktu' => $masaStudiBulan <= $batasTepat,
        ];
    }

    /**
     * Ambil jenjang prodi mahasiswa dari enrollment terakhir, atau fallback ke 'D4'.
     */
    private function ambilJenjangMahasiswa(Mahasiswa $mhs, mixed $prodiId = null): string
    {
        // Cari dari enrollment melalui relasi angkatanKelas->prodi
        $enrollment = EnrollmentKelas::where('id_mahasiswa', $mhs->id)
            ->with('angkatanKelas.prodi')
            ->when($prodiId, fn ($q) => $q->where('prodi_id', $prodiId))
            ->latest()
            ->first();

        return $enrollment?->angkatanKelas?->prodi?->jenjang ?? 'D4';
    }

    /**
     * Batas masa studi tepat waktu (bulan) berdasarkan jenjang.
     * D3 = 36 bulan, semua D4/S1 dan lainnya = 48 bulan.
     */
    private function batasMasaStudiTepat(string $jenjang): int
    {
        return match ($jenjang) {
            'D3' => 36,
            default => 48,
        };
    }
}
