<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\MataKuliah;
use Illuminate\Http\Request;

class MataKuliahCplController extends Controller
{
    // Menampilkan daftar CPL yang sudah terikat pada suatu Mata Kuliah tertentu
    public function index(string $mataKuliahId)
    {
        $matkul = MataKuliah::with('cpl')->find($mataKuliahId);

        if (! $matkul) {
            return ApiResponse::error('Data Mata Kuliah tidak ditemukan', 404);
        }

        return ApiResponse::success($matkul->cpl, 'Berhasil mengambil data mapping CPL Mata Kuliah');
    }

    // Melakukan Mapping / Sync CPL berdasarkan centangan dari frontend
    public function sync(Request $request, string $mataKuliahId)
    {
        $matkul = MataKuliah::find($mataKuliahId);

        if (! $matkul) {
            return ApiResponse::error('Data Mata Kuliah tidak ditemukan', 404);
        }

        $validated = $request->validate([
            'cpl_ids' => 'required_without:mappings|array',
            'cpl_ids.*' => 'required|integer|distinct|exists:cpl,id',
            'mappings' => 'required_without:cpl_ids|array',
            'mappings.*.id_cpl' => 'required|integer|distinct|exists:cpl,id',
            'mappings.*.bobot' => 'required|numeric|gt:0|lte:100',
        ]);

        if (isset($validated['mappings'])) {
            $syncData = collect($validated['mappings'])
                ->mapWithKeys(fn (array $mapping): array => [
                    $mapping['id_cpl'] => ['bobot' => $mapping['bobot']],
                ])
                ->all();
        } else {
            $existingMappings = $matkul->cpl()->get()->keyBy('id');
            $syncData = collect($validated['cpl_ids'])
                ->mapWithKeys(fn (int $cplId): array => [
                    $cplId => ['bobot' => $existingMappings->get($cplId)?->pivot->bobot ?? 100],
                ])
                ->all();
        }

        $matkul->cpl()->sync($syncData);

        return ApiResponse::success($matkul->load('cpl'), 'Mapping CPL Mata Kuliah berhasil disimpan');
    }
}
