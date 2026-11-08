<?php

namespace App\Http\Controllers;

use App\Enums\JenisEvidencePrestasi;
use App\Helpers\ApiResponse;
use App\Models\PrestasiMahasiswa;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PrestasiMahasiswaController extends Controller
{
    /**
     * Tampil semua data prestasi mahasiswa beserta relasi mahasiswa dan evidence.
     */
    public function index(): JsonResponse
    {
        $data = PrestasiMahasiswa::with(['mahasiswa', 'evidence', 'jenisPrestasi'])->get();

        return ApiResponse::success($data, 'Berhasil mengambil data Prestasi Mahasiswa');
    }

    /**
     * Tambah data prestasi mahasiswa baru beserta batch evidence jika ada.
     */
    public function store(Request $request): JsonResponse
    {
        $this->normalizeEvidencePayload($request);

        $validated = $request->validate([
            'id_mahasiswa' => 'required|exists:data_mahasiswa,id',
            'nama_aktivitas' => 'required|string|max:255',
            'tahun' => 'required|integer',
            'evidence' => 'nullable|array',
            'evidence.*.id' => 'nullable|integer',
            'evidence.*.url' => 'required|url|max:2048',
            'evidence.*.jenis_evidence' => ['nullable', 'string', Rule::in(JenisEvidencePrestasi::values())],
            'evidence.*.judul' => 'nullable|string|max:255',
        ], [
            'id_mahasiswa.required' => 'Mahasiswa wajib dipilih.',
            'id_mahasiswa.exists' => 'Data mahasiswa tidak valid.',
            'nama_aktivitas.required' => 'Nama aktivitas atau prestasi wajib diisi.',
            'tahun.required' => 'Tahun perolehan prestasi wajib diisi.',
            'evidence.*.url.required' => 'URL berkas bukti prestasi wajib diisi.',
            'evidence.*.url.url' => 'Format URL berkas bukti prestasi tidak valid.',
            'evidence.*.url.max' => 'URL berkas bukti prestasi maksimal 2048 karakter.',
            'evidence.*.jenis_evidence.in' => 'Jenis evidence prestasi tidak valid.',
        ]);

        $prestasi = DB::transaction(function () use ($validated) {
            $prestasi = PrestasiMahasiswa::create([
                'id_mahasiswa' => $validated['id_mahasiswa'],
                'nama_aktivitas' => $validated['nama_aktivitas'],
                'tahun' => $validated['tahun'],
            ]);

            if (! empty($validated['evidence'])) {
                foreach ($validated['evidence'] as $item) {
                    $prestasi->evidence()->create([
                        'url' => $item['url'],
                        'jenis_evidence' => $item['jenis_evidence'] ?? JenisEvidencePrestasi::SERTIFIKAT->value,
                        'judul' => $item['judul'] ?? null,
                    ]);
                }
            }

            return $prestasi;
        });

        $prestasi->load(['mahasiswa', 'evidence', 'jenisPrestasi']);

        return ApiResponse::success($prestasi, 'Data Prestasi Mahasiswa berhasil ditambahkan', 201);
    }

    /**
     * Tampil satu data spesifik prestasi mahasiswa.
     */
    public function show(string $id): JsonResponse
    {
        $prestasi = PrestasiMahasiswa::with(['mahasiswa', 'evidence', 'jenisPrestasi'])->find($id);

        if (! $prestasi) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        return ApiResponse::success($prestasi, 'Berhasil mengambil detail data Prestasi Mahasiswa');
    }

    /**
     * Update data prestasi mahasiswa beserta batch evidence.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $prestasi = PrestasiMahasiswa::find($id);

        if (! $prestasi) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        $this->normalizeEvidencePayload($request);

        $validated = $request->validate([
            'id_mahasiswa' => 'required|exists:data_mahasiswa,id',
            'nama_aktivitas' => 'required|string|max:255',
            'tahun' => 'required|integer',
            'evidence' => 'nullable|array',
            'evidence.*.id' => 'nullable|integer',
            'evidence.*.url' => 'required|url|max:2048',
            'evidence.*.jenis_evidence' => ['nullable', 'string', Rule::in(JenisEvidencePrestasi::values())],
            'evidence.*.judul' => 'nullable|string|max:255',
        ], [
            'id_mahasiswa.required' => 'Mahasiswa wajib dipilih.',
            'id_mahasiswa.exists' => 'Data mahasiswa tidak valid.',
            'nama_aktivitas.required' => 'Nama aktivitas atau prestasi wajib diisi.',
            'tahun.required' => 'Tahun perolehan prestasi wajib diisi.',
            'evidence.*.url.required' => 'URL berkas bukti prestasi wajib diisi.',
            'evidence.*.url.url' => 'Format URL berkas bukti prestasi tidak valid.',
            'evidence.*.url.max' => 'URL berkas bukti prestasi maksimal 2048 karakter.',
            'evidence.*.jenis_evidence.in' => 'Jenis evidence prestasi tidak valid.',
        ]);

        $prestasi = DB::transaction(function () use ($prestasi, $validated, $request) {
            $prestasi->update([
                'id_mahasiswa' => $validated['id_mahasiswa'],
                'nama_aktivitas' => $validated['nama_aktivitas'],
                'tahun' => $validated['tahun'],
            ]);

            if ($request->has('evidence')) {
                $evidenceData = $validated['evidence'] ?? [];
                $keptIds = [];

                foreach ($evidenceData as $item) {
                    if (! empty($item['id'])) {
                        $existing = $prestasi->evidence()->where('id', $item['id'])->first();
                        if ($existing) {
                            $existing->update([
                                'url' => $item['url'],
                                'jenis_evidence' => $item['jenis_evidence'] ?? JenisEvidencePrestasi::SERTIFIKAT->value,
                                'judul' => $item['judul'] ?? null,
                            ]);
                            $keptIds[] = $existing->id;

                            continue;
                        }
                    }

                    $created = $prestasi->evidence()->create([
                        'url' => $item['url'],
                        'jenis_evidence' => $item['jenis_evidence'] ?? JenisEvidencePrestasi::SERTIFIKAT->value,
                        'judul' => $item['judul'] ?? null,
                    ]);
                    $keptIds[] = $created->id;
                }

                $prestasi->evidence()->whereNotIn('id', $keptIds)->delete();
            }

            return $prestasi;
        });

        $prestasi->load(['mahasiswa', 'evidence', 'jenisPrestasi']);

        return ApiResponse::success($prestasi, 'Data Prestasi Mahasiswa berhasil diupdate');
    }

    /**
     * Hapus data prestasi mahasiswa dan berkas buktinya.
     */
    public function destroy(string $id): JsonResponse
    {
        $prestasi = PrestasiMahasiswa::find($id);

        if (! $prestasi) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        try {
            DB::transaction(function () use ($prestasi) {
                $prestasi->evidence()->delete();
                $prestasi->delete();
            });

            return ApiResponse::success(null, 'Data berhasil dihapus');
        } catch (QueryException $e) {
            return ApiResponse::error('Gagal menghapus data karena masih terikat relasi', 400);
        }
    }

    /**
     * Normalisasi payload batch evidence agar mendukung format string url maupun objek.
     */
    protected function normalizeEvidencePayload(Request $request): void
    {
        if (! $request->has('evidence') || is_null($request->input('evidence'))) {
            return;
        }

        $rawEvidence = $request->input('evidence');
        if (! is_array($rawEvidence)) {
            $rawEvidence = [$rawEvidence];
        }

        $normalized = array_map(function ($item) {
            if (is_string($item)) {
                return [
                    'url' => trim($item),
                    'jenis_evidence' => JenisEvidencePrestasi::SERTIFIKAT->value,
                    'judul' => null,
                ];
            }

            if (is_array($item)) {
                return [
                    'id' => $item['id'] ?? null,
                    'url' => isset($item['url']) ? trim((string) $item['url']) : (isset($item['evidence']) ? trim((string) $item['evidence']) : null),
                    'jenis_evidence' => $item['jenis_evidence'] ?? JenisEvidencePrestasi::SERTIFIKAT->value,
                    'judul' => $item['judul'] ?? null,
                ];
            }

            return $item;
        }, $rawEvidence);

        $request->merge(['evidence' => $normalized]);
    }
}
