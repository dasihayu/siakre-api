<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\Mahasiswa;
use App\Models\NilaiMahasiswa;
use Illuminate\Http\Request;

class NilaiMahasiswaController extends Controller
{
    /**
     * Menampilkan seluruh data nilai mahasiswa (bisa difilter per kelas, mahasiswa, atau semester).
     */
    public function index(Request $request)
    {
        $query = NilaiMahasiswa::with([
            'mataKuliahAngkatanKelas.enrollmentMataKuliah.mataKuliah',
            'mataKuliahAngkatanKelas.enrollmentMataKuliah.tahunAkademik',
            'mataKuliahAngkatanKelas.angkatanKelas.kelas',
            'mahasiswa',
        ]);

        if ($request->has('mata_kuliah_angkatan_kelas_id')) {
            $query->where('mata_kuliah_angkatan_kelas_id', $request->query('mata_kuliah_angkatan_kelas_id'));
        }

        if ($request->has('mahasiswa_id')) {
            $query->where('mahasiswa_id', $request->query('mahasiswa_id'));
        }

        if ($request->has('semester')) {
            $query->where('semester', $request->query('semester'));
        }

        $nilai = $query->get();

        return ApiResponse::success($nilai, 'Daftar nilai mahasiswa berhasil diambil');
    }

    /**
     * Menyimpan data nilai mahasiswa baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'mata_kuliah_angkatan_kelas_id' => 'required|integer|exists:mata_kuliah_angkatan_kelas,id',
            'mahasiswa_id' => 'required|integer|exists:data_mahasiswa,id',
            'semester' => 'nullable|integer|min:1|max:14',
            'nilai_angka' => 'nullable|numeric|min:0|max:100',
            'nilai_huruf' => 'nullable|string|max:5',
            'bobot' => 'nullable|numeric|min:0|max:4',
            'link_nilai' => 'nullable|string|max:2048',
            'link_evidence' => 'nullable|string|max:2048',
        ]);

        $validated['semester'] = $validated['semester'] ?? 1;

        if (isset($validated['nilai_angka']) && empty($validated['nilai_huruf']) && ! isset($validated['bobot'])) {
            $calc = NilaiMahasiswa::hitungNilaiHurufDanBobot((float) $validated['nilai_angka']);
            $validated['nilai_huruf'] = $calc['nilai_huruf'];
            $validated['bobot'] = $calc['bobot'];
        }

        $nilai = NilaiMahasiswa::create($validated);
        $nilai->load(['mataKuliahAngkatanKelas', 'mahasiswa']);

        return ApiResponse::success($nilai, 'Nilai mahasiswa berhasil ditambahkan', 201);
    }

    /**
     * Menyimpan atau memperbarui nilai banyak mahasiswa sekaligus (Batch Input).
     */
    public function batchStore(Request $request)
    {
        $validated = $request->validate([
            'mata_kuliah_angkatan_kelas_id' => 'required|integer|exists:mata_kuliah_angkatan_kelas,id',
            'semester' => 'required|integer|min:1|max:14',
            'nilai' => 'required|array|min:1',
            'nilai.*.mahasiswa_id' => 'required|integer|exists:data_mahasiswa,id',
            'nilai.*.nilai_angka' => 'nullable|numeric|min:0|max:100',
            'nilai.*.nilai_huruf' => 'nullable|string|max:5',
            'nilai.*.bobot' => 'nullable|numeric|min:0|max:4',
            'nilai.*.link_nilai' => 'nullable|string|max:2048',
            'nilai.*.link_evidence' => 'nullable|string|max:2048',
        ]);

        $mkAkId = $validated['mata_kuliah_angkatan_kelas_id'];
        $semester = $validated['semester'];
        $results = [];

        foreach ($validated['nilai'] as $item) {
            $data = [
                'semester' => $semester,
                'nilai_angka' => $item['nilai_angka'] ?? null,
                'nilai_huruf' => $item['nilai_huruf'] ?? null,
                'bobot' => $item['bobot'] ?? null,
                'link_nilai' => $item['link_nilai'] ?? null,
                'link_evidence' => $item['link_evidence'] ?? null,
            ];

            if (isset($data['nilai_angka']) && empty($data['nilai_huruf']) && ! isset($data['bobot'])) {
                $calc = NilaiMahasiswa::hitungNilaiHurufDanBobot((float) $data['nilai_angka']);
                $data['nilai_huruf'] = $calc['nilai_huruf'];
                $data['bobot'] = $calc['bobot'];
            }

            $nilai = NilaiMahasiswa::updateOrCreate(
                [
                    'mata_kuliah_angkatan_kelas_id' => $mkAkId,
                    'mahasiswa_id' => $item['mahasiswa_id'],
                ],
                $data
            );

            $results[] = $nilai;
        }

        return ApiResponse::success($results, 'Batch nilai mahasiswa berhasil disimpan', 201);
    }

    /**
     * Menampilkan detail data nilai mahasiswa spesifik.
     */
    public function show(string $id)
    {
        $nilai = NilaiMahasiswa::with([
            'mataKuliahAngkatanKelas.enrollmentMataKuliah.mataKuliah',
            'mataKuliahAngkatanKelas.enrollmentMataKuliah.tahunAkademik',
            'mataKuliahAngkatanKelas.angkatanKelas.kelas',
            'mahasiswa',
        ])->find($id);

        if (! $nilai) {
            return ApiResponse::error('Data nilai mahasiswa tidak ditemukan', 404);
        }

        return ApiResponse::success($nilai, 'Detail data nilai mahasiswa berhasil diambil');
    }

    /**
     * Memperbarui seluruh/sebagian data nilai mahasiswa.
     */
    public function update(Request $request, string $id)
    {
        $nilai = NilaiMahasiswa::find($id);

        if (! $nilai) {
            return ApiResponse::error('Data nilai mahasiswa tidak ditemukan', 404);
        }

        $validated = $request->validate([
            'mata_kuliah_angkatan_kelas_id' => 'sometimes|required|integer|exists:mata_kuliah_angkatan_kelas,id',
            'mahasiswa_id' => 'sometimes|required|integer|exists:data_mahasiswa,id',
            'semester' => 'sometimes|required|integer|min:1|max:14',
            'nilai_angka' => 'nullable|numeric|min:0|max:100',
            'nilai_huruf' => 'nullable|string|max:5',
            'bobot' => 'nullable|numeric|min:0|max:4',
            'link_nilai' => 'nullable|string|max:2048',
            'link_evidence' => 'nullable|string|max:2048',
        ]);

        if (isset($validated['nilai_angka']) && empty($validated['nilai_huruf']) && ! isset($validated['bobot'])) {
            $calc = NilaiMahasiswa::hitungNilaiHurufDanBobot((float) $validated['nilai_angka']);
            $validated['nilai_huruf'] = $calc['nilai_huruf'];
            $validated['bobot'] = $calc['bobot'];
        }

        $nilai->update($validated);
        $nilai->load(['mataKuliahAngkatanKelas', 'mahasiswa']);

        return ApiResponse::success($nilai, 'Data nilai mahasiswa berhasil diperbarui');
    }

    /**
     * Menghapus data nilai mahasiswa.
     */
    public function destroy(string $id)
    {
        $nilai = NilaiMahasiswa::find($id);

        if (! $nilai) {
            return ApiResponse::error('Data nilai mahasiswa tidak ditemukan', 404);
        }

        $nilai->delete();

        return ApiResponse::success(null, 'Data nilai mahasiswa berhasil dihapus');
    }

    /**
     * Endpoint khusus untuk mengupdate link_nilai mahasiswa.
     */
    public function updateLinkNilai(Request $request, string $id)
    {
        $nilai = NilaiMahasiswa::find($id);

        if (! $nilai) {
            return ApiResponse::error('Data nilai mahasiswa tidak ditemukan', 404);
        }

        $validated = $request->validate([
            'link_nilai' => 'required|string|max:2048',
        ]);

        $nilai->update([
            'link_nilai' => $validated['link_nilai'],
        ]);

        return ApiResponse::success($nilai, 'Link nilai mahasiswa berhasil diperbarui');
    }

    /**
     * Endpoint khusus untuk mengupdate link_evidence mahasiswa.
     */
    public function updateLinkEvidence(Request $request, string $id)
    {
        $nilai = NilaiMahasiswa::find($id);

        if (! $nilai) {
            return ApiResponse::error('Data nilai mahasiswa tidak ditemukan', 404);
        }

        $validated = $request->validate([
            'link_evidence' => 'required|string|max:2048',
        ]);

        $nilai->update([
            'link_evidence' => $validated['link_evidence'],
        ]);

        return ApiResponse::success($nilai, 'Link evidence mahasiswa berhasil diperbarui');
    }

    /**
     * Menampilkan Kartu Hasil Studi (KHS) mahasiswa per semester.
     */
    public function khs(Request $request, string $id)
    {
        $mahasiswa = Mahasiswa::find($id);

        if (! $mahasiswa) {
            return ApiResponse::error('Data mahasiswa tidak ditemukan', 404);
        }

        $semester = $request->query('semester');

        $query = NilaiMahasiswa::with([
            'mataKuliahAngkatanKelas.enrollmentMataKuliah.mataKuliah',
            'mataKuliahAngkatanKelas.enrollmentMataKuliah.tahunAkademik',
        ])->where('mahasiswa_id', $id);

        if ($semester) {
            $query->where('semester', $semester);
        }

        $nilaiList = $query->get();

        $totalSks = 0;
        $totalBobotSks = 0;

        $items = $nilaiList->map(function ($item) use (&$totalSks, &$totalBobotSks) {
            $mk = $item->mataKuliahAngkatanKelas?->enrollmentMataKuliah?->mataKuliah;
            $sks = $mk?->sks ?? 0;
            $bobot = $item->bobot ?? 0;

            $totalSks += $sks;
            $totalBobotSks += ($sks * $bobot);

            return [
                'id' => $item->id,
                'semester' => $item->semester,
                'kode_mata_kuliah' => $mk?->kode_mata_kuliah,
                'nama_mata_kuliah' => $mk?->nama_mata_kuliah,
                'sks' => $sks,
                'nilai_angka' => $item->nilai_angka,
                'nilai_huruf' => $item->nilai_huruf,
                'bobot' => $item->bobot,
                'link_nilai' => $item->link_nilai,
                'link_evidence' => $item->link_evidence,
            ];
        });

        $ips = $totalSks > 0 ? round($totalBobotSks / $totalSks, 2) : 0;

        return ApiResponse::success([
            'mahasiswa' => [
                'id' => $mahasiswa->id,
                'nim' => $mahasiswa->nim,
                'nama' => $mahasiswa->nama_mahasiswa,
            ],
            'semester' => $semester ? (int) $semester : 'Semua',
            'total_sks' => $totalSks,
            'ips' => $ips,
            'daftar_nilai' => $items,
        ], 'Kartu Hasil Studi (KHS) berhasil diambil');
    }

    /**
     * Menampilkan Transkrip Nilai Kumulatif mahasiswa.
     */
    public function transkrip(string $id)
    {
        $mahasiswa = Mahasiswa::find($id);

        if (! $mahasiswa) {
            return ApiResponse::error('Data mahasiswa tidak ditemukan', 404);
        }

        $nilaiList = NilaiMahasiswa::with([
            'mataKuliahAngkatanKelas.enrollmentMataKuliah.mataKuliah',
            'mataKuliahAngkatanKelas.enrollmentMataKuliah.tahunAkademik',
        ])
            ->where('mahasiswa_id', $id)
            ->orderBy('semester')
            ->get();

        $totalSks = 0;
        $totalBobotSks = 0;

        $items = $nilaiList->map(function ($item) use (&$totalSks, &$totalBobotSks) {
            $mk = $item->mataKuliahAngkatanKelas?->enrollmentMataKuliah?->mataKuliah;
            $sks = $mk?->sks ?? 0;
            $bobot = $item->bobot ?? 0;

            $totalSks += $sks;
            $totalBobotSks += ($sks * $bobot);

            return [
                'id' => $item->id,
                'semester' => $item->semester,
                'kode_mata_kuliah' => $mk?->kode_mata_kuliah,
                'nama_mata_kuliah' => $mk?->nama_mata_kuliah,
                'sks' => $sks,
                'nilai_angka' => $item->nilai_angka,
                'nilai_huruf' => $item->nilai_huruf,
                'bobot' => $item->bobot,
                'link_nilai' => $item->link_nilai,
                'link_evidence' => $item->link_evidence,
            ];
        });

        $ipk = $totalSks > 0 ? round($totalBobotSks / $totalSks, 2) : 0;

        return ApiResponse::success([
            'mahasiswa' => [
                'id' => $mahasiswa->id,
                'nim' => $mahasiswa->nim,
                'nama' => $mahasiswa->nama_mahasiswa,
            ],
            'total_sks' => $totalSks,
            'ipk' => $ipk,
            'daftar_nilai' => $items,
        ], 'Transkrip nilai mahasiswa berhasil diambil');
    }
}
