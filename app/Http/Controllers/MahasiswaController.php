<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\Mahasiswa;
use App\Services\AkademikCalculatorService;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class MahasiswaController extends Controller
{
    /**
     * GET /api/mahasiswa/tren
     *
     * Tren riil berbasis data nyata dari TS sampai TS-n:
     * - jumlah_mahasiswa_baru   : jumlah mahasiswa yang masuk per tahun
     * - rata_ipk_lulusan        : rata-rata IPK lulusan per kohort angkatan
     * - rata_masa_studi_bulan   : rata-rata masa studi (bulan) lulusan per kohort
     * - persen_lulus_tepat_waktu: % lulus tepat waktu per kohort (D3≤36bln, D4/S1≤48bln)
     *
     * Query params:
     *   n (int, default 5) : jumlah tahun ke belakang dari tahun berjalan
     */
    public function tren(Request $request)
    {
        $n = max(1, (int) $request->query('n', 5));
        $tsBulan = (int) date('Y');
        $years = range($tsBulan - $n + 1, $tsBulan);

        $jumlahMahasiswaBaru = [];
        $rataIpkLulusan = [];
        $rataMasaStudiBulan = [];
        $persenLulusTepat = [];

        foreach ($years as $year) {
            // ── Mahasiswa baru per tahun masuk ──────────────────────────────
            $mahasiswaBaru = Mahasiswa::where('tahun_masuk', $year)->count();
            $jumlahMahasiswaBaru[] = $mahasiswaBaru;

            // ── Lulusan dari kohort angkatan $year ───────────────────────────
            // Lulusan adalah mahasiswa kohort $year yang sudah memiliki tanggal_kelulusan
            $lulusanKohort = Mahasiswa::where('tahun_masuk', $year)
                ->whereNotNull('tanggal_kelulusan')
                ->with([
                    'nilaiMahasiswa.mataKuliahAngkatanKelas.enrollmentMataKuliah.mataKuliah',
                    'nilaiMahasiswa.tahunAkademik',
                ])
                ->get();

            $totalLulusan = $lulusanKohort->count();

            if ($totalLulusan === 0) {
                $rataIpkLulusan[] = null;
                $rataMasaStudiBulan[] = null;
                $persenLulusTepat[] = null;
            } else {
                // Hitung IPK tiap lulusan lalu rata-rata
                $sumIpk = 0.0;
                $sumMasaStudi = 0;
                $tepatCount = 0;

                foreach ($lulusanKohort as $mhs) {
                    // IPK riil
                    $ipkResult = AkademikCalculatorService::hitungIPK($mhs->nilaiMahasiswa);
                    $sumIpk += $ipkResult['ipk'];

                    // Masa studi aktual
                    $masaStudiBulan = $this->hitungMasaStudiBulan($mhs);
                    $sumMasaStudi += $masaStudiBulan;

                    // Batas tepat waktu (default D4/S1 = 48, D3 = 36)
                    // Karena bisa multi-prodi, kita pakai default 48 untuk kohort global
                    // Controller Dashboard mengambil per-prodi; di sini kita gunakan kolom masa_studi_bulan
                    if ($masaStudiBulan <= 48) {
                        $tepatCount++;
                    }
                }

                $rataIpkLulusan[] = round($sumIpk / $totalLulusan, 2);
                $rataMasaStudiBulan[] = round($sumMasaStudi / $totalLulusan, 1);
                $persenLulusTepat[] = round(($tepatCount / $totalLulusan) * 100, 1);
            }
        }

        return ApiResponse::success([
            'years' => array_values($years),
            'jumlah_mahasiswa_baru' => $jumlahMahasiswaBaru,
            'rata_ipk_lulusan' => $rataIpkLulusan,
            'rata_masa_studi_bulan' => $rataMasaStudiBulan,
            'persen_lulus_tepat_waktu' => $persenLulusTepat,
        ], 'Berhasil mengambil tren riil mahasiswa');
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

    public function index(Request $request)
    {
        $request->validate([
            'kategori' => ['nullable', 'string', 'in:baru,transfer,asing'],
        ]);

        $data = Mahasiswa::kategori($request->query('kategori'))->get();

        return ApiResponse::success($data, 'Berhasil mengambil data Mahasiswa');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nim' => 'required|integer|unique:data_mahasiswa,nim',
            'nama_mahasiswa' => 'required|string',
            'tahun_masuk' => 'required|integer',
            'jenis_pendaftaran' => 'required|string',
            'tanggal_kelulusan' => 'nullable|date',
            'masa_studi_bulan' => 'required|integer',
        ], [
            'nim.unique' => 'NIM Mahasiswa Sudah Ada',
        ]);

        $mhs = Mahasiswa::create($request->all());

        return ApiResponse::success($mhs, 'Data Mahasiswa berhasil ditambahkan', 201);
    }

    public function show(string $id)
    {
        $mhs = Mahasiswa::find($id);

        if (! $mhs) {
            return ApiResponse::error('Data mahasiswa tidak ditemukan', 404);
        }

        return ApiResponse::success($mhs, 'Berhasil mengambil detail data Mahasiswa');
    }

    public function update(Request $request, string $id)
    {
        $mhs = Mahasiswa::find($id);

        if (! $mhs) {
            return ApiResponse::error('Data mahasiswa tidak ditemukan', 404);
        }

        $request->validate([
            'nim' => 'required|integer|unique:data_mahasiswa,nim,'.$id,
            'nama_mahasiswa' => 'required|string',
            'tahun_masuk' => 'required|integer',
            'jenis_pendaftaran' => 'required|string',
            'tanggal_kelulusan' => 'nullable|date',
            'masa_studi_bulan' => 'required|integer',
        ], [
            'nim.unique' => 'NIM Mahasiswa Sudah Ada',
        ]);

        $mhs->update($request->all());

        return ApiResponse::success($mhs, 'Data Mahasiswa berhasil diupdate');
    }

    public function destroy(string $id)
    {
        $mhs = Mahasiswa::find($id);

        if (! $mhs) {
            return ApiResponse::error('Data mahasiswa tidak ditemukan', 404);
        }

        try {
            $mhs->delete();

            return ApiResponse::success(null, 'Data mahasiswa berhasil dihapus');

        } catch (QueryException $e) {
            return ApiResponse::error('Gagal menghapus data karena masih terikat relasi', 400);
        }
    }
}
