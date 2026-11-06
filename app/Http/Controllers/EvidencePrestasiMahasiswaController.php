<?php

namespace App\Http\Controllers;

use App\Enums\JenisEvidencePrestasi;
use App\Helpers\ApiResponse;
use App\Models\EvidencePrestasiMahasiswa;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EvidencePrestasiMahasiswaController extends Controller
{
    /**
     * Tampil semua data evidence prestasi mahasiswa.
     */
    public function index(): JsonResponse
    {
        $data = EvidencePrestasiMahasiswa::with(['prestasiMahasiswa'])->get();

        return ApiResponse::success($data, 'Berhasil mengambil data Evidence Prestasi Mahasiswa');
    }

    /**
     * Tambah data evidence prestasi mahasiswa baru.
     */
    public function store(Request $request): JsonResponse
    {
        if ($request->has('evidence') && ! $request->has('url')) {
            $request->merge(['url' => $request->input('evidence')]);
        }

        $validated = $request->validate([
            'id_prestasi_mahasiswa' => 'required|exists:prestasi_mahasiswa,id',
            'url' => 'required|url|max:2048',
            'jenis_evidence' => ['nullable', 'string', Rule::in(JenisEvidencePrestasi::values())],
            'judul' => 'nullable|string|max:255',
        ], [
            'id_prestasi_mahasiswa.required' => 'Prestasi mahasiswa wajib dipilih.',
            'id_prestasi_mahasiswa.exists' => 'Data prestasi mahasiswa tidak valid.',
            'url.required' => 'URL berkas bukti prestasi wajib diisi.',
            'url.url' => 'Format URL berkas bukti prestasi tidak valid.',
            'url.max' => 'URL berkas bukti prestasi maksimal 2048 karakter.',
            'jenis_evidence.in' => 'Jenis evidence prestasi tidak valid.',
        ]);

        if (empty($validated['jenis_evidence'])) {
            $validated['jenis_evidence'] = JenisEvidencePrestasi::SERTIFIKAT->value;
        }

        $evidence = EvidencePrestasiMahasiswa::create($validated);

        return ApiResponse::success($evidence, 'Data Evidence Prestasi Mahasiswa berhasil ditambahkan', 201);
    }

    /**
     * Tampil satu data spesifik evidence prestasi mahasiswa.
     */
    public function show(string $id): JsonResponse
    {
        $evidence = EvidencePrestasiMahasiswa::with(['prestasiMahasiswa'])->find($id);

        if (! $evidence) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        return ApiResponse::success($evidence, 'Berhasil mengambil detail data Evidence Prestasi Mahasiswa');
    }

    /**
     * Update data evidence prestasi mahasiswa.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $evidence = EvidencePrestasiMahasiswa::find($id);

        if (! $evidence) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        if ($request->has('evidence') && ! $request->has('url')) {
            $request->merge(['url' => $request->input('evidence')]);
        }

        $validated = $request->validate([
            'id_prestasi_mahasiswa' => 'sometimes|required|exists:prestasi_mahasiswa,id',
            'url' => 'sometimes|required|url|max:2048',
            'jenis_evidence' => ['nullable', 'string', Rule::in(JenisEvidencePrestasi::values())],
            'judul' => 'nullable|string|max:255',
        ], [
            'id_prestasi_mahasiswa.required' => 'Prestasi mahasiswa wajib dipilih.',
            'id_prestasi_mahasiswa.exists' => 'Data prestasi mahasiswa tidak valid.',
            'url.required' => 'URL berkas bukti prestasi wajib diisi.',
            'url.url' => 'Format URL berkas bukti prestasi tidak valid.',
            'url.max' => 'URL berkas bukti prestasi maksimal 2048 karakter.',
            'jenis_evidence.in' => 'Jenis evidence prestasi tidak valid.',
        ]);

        $evidence->update($validated);

        return ApiResponse::success($evidence, 'Data Evidence Prestasi Mahasiswa berhasil diupdate');
    }

    /**
     * Hapus data evidence prestasi mahasiswa.
     */
    public function destroy(string $id): JsonResponse
    {
        $evidence = EvidencePrestasiMahasiswa::find($id);

        if (! $evidence) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        $evidence->delete();

        return ApiResponse::success(null, 'Data berhasil dihapus');
    }
}
