<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\NilaiMahasiswa;
use Illuminate\Http\Request;

class NilaiMahasiswaController extends Controller
{
    /**
     * Menampilkan seluruh data nilai mahasiswa.
     */
    public function index()
    {
        $nilai = NilaiMahasiswa::with('mataKuliahAngkatanKelas')->get();

        return ApiResponse::success($nilai, 'Daftar nilai mahasiswa berhasil diambil');
    }

    /**
     * Menyimpan data nilai mahasiswa baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'mata_kuliah_angkatan_kelas_id' => 'required|integer|exists:mata_kuliah_angkatan_kelas,id',
            'link_nilai' => 'nullable|string|max:2048',
            'link_evidence' => 'nullable|string|max:2048',
        ]);

        $nilai = NilaiMahasiswa::create($validated);

        return ApiResponse::success($nilai, 'Nilai mahasiswa berhasil ditambahkan', 201);
    }

    /**
     * Menampilkan detail data nilai mahasiswa spesifik.
     */
    public function show(string $id)
    {
        $nilai = NilaiMahasiswa::with('mataKuliahAngkatanKelas')->find($id);

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
            'link_nilai' => 'nullable|string|max:2048',
            'link_evidence' => 'nullable|string|max:2048',
        ]);

        $nilai->update($validated);

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
}
