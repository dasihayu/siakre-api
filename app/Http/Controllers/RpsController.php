<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\MataKuliah;
use App\Models\Rps;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RpsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id_mata_kuliah' => 'sometimes|integer|exists:mata_kuliah,id',
        ]);

        $rps = Rps::query()
            ->with('mataKuliah')
            ->when(
                isset($validated['id_mata_kuliah']),
                fn ($query) => $query->where('id_mata_kuliah', $validated['id_mata_kuliah'])
            )
            ->orderBy('id_mata_kuliah')
            ->orderByDesc('tahun_berlaku')
            ->orderByDesc('versi')
            ->get();

        return ApiResponse::success($rps, 'Berhasil mengambil data RPS');
    }

    public function store(Request $request): JsonResponse
    {
        $rps = Rps::create($this->validatedData($request));

        return ApiResponse::success($rps->load('mataKuliah'), 'RPS berhasil ditambahkan', 201);
    }

    public function indexByMataKuliah(string $mataKuliahId): JsonResponse
    {
        $mataKuliah = MataKuliah::find($mataKuliahId);

        if ($mataKuliah === null) {
            return ApiResponse::error('Data Mata Kuliah tidak ditemukan', 404);
        }

        return ApiResponse::success(
            $mataKuliah->rps()->orderByDesc('tahun_berlaku')->orderByDesc('versi')->get(),
            'Berhasil mengambil data RPS Mata Kuliah'
        );
    }

    public function storeByMataKuliah(Request $request, string $mataKuliahId): JsonResponse
    {
        $mataKuliah = MataKuliah::find($mataKuliahId);

        if ($mataKuliah === null) {
            return ApiResponse::error('Data Mata Kuliah tidak ditemukan', 404);
        }

        $rps = $mataKuliah->rps()->create($this->validatedData($request, false));

        return ApiResponse::success($rps, 'RPS berhasil ditambahkan', 201);
    }

    public function show(string $id): JsonResponse
    {
        $rps = Rps::with('mataKuliah')->find($id);

        if ($rps === null) {
            return ApiResponse::error('Data RPS tidak ditemukan', 404);
        }

        return ApiResponse::success($rps, 'Berhasil mengambil detail RPS');
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $rps = Rps::find($id);

        if ($rps === null) {
            return ApiResponse::error('Data RPS tidak ditemukan', 404);
        }

        $rps->update($this->validatedData($request));

        return ApiResponse::success($rps->load('mataKuliah'), 'RPS berhasil diperbarui');
    }

    public function destroy(string $id): JsonResponse
    {
        $rps = Rps::find($id);

        if ($rps === null) {
            return ApiResponse::error('Data RPS tidak ditemukan', 404);
        }

        $rps->delete();

        return ApiResponse::success(null, 'RPS berhasil dihapus');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedData(Request $request, bool $includeMataKuliah = true): array
    {
        $rules = [
            'versi' => 'required|string|max:50',
            'tahun_berlaku' => 'required|integer|between:2000,2100',
            'dokumen_url' => 'nullable|string|max:2048',
            'status' => 'required|in:Aktif,Draft,Arsip',
        ];

        if ($includeMataKuliah) {
            $rules['id_mata_kuliah'] = 'required|integer|exists:mata_kuliah,id';
        }

        return $request->validate($rules);
    }
}
