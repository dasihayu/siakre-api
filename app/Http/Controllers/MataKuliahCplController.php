<?php

namespace App\Http\Controllers;

use App\Models\MataKuliah;
use Illuminate\Http\Request;
use App\Helpers\ApiResponse;

class MataKuliahCplController extends Controller
{
    // Menampilkan daftar CPL yang sudah terikat pada suatu Mata Kuliah tertentu
    public function index(string $mataKuliahId)
    {
        $matkul = MataKuliah::with('cpl')->find($mataKuliahId);

        if (!$matkul) {
            return ApiResponse::error('Data Mata Kuliah tidak ditemukan', 404);
        }

        return ApiResponse::success($matkul->cpl, 'Berhasil mengambil data mapping CPL Mata Kuliah');
    }

    // Melakukan Mapping / Sync CPL berdasarkan centangan dari frontend
    public function sync(Request $request, string $mataKuliahId)
    {
        $matkul = MataKuliah::find($mataKuliahId);

        if (!$matkul) {
            return ApiResponse::error('Data Mata Kuliah tidak ditemukan', 404);
        }

        $request->validate([
            'cpl_ids'   => 'required|array',
            'cpl_ids.*' => 'exists:cpl,id', // Pastikan setiap ID CPL benar-benar ada di database
        ]);

        // Fungsi sync Laravel otomatis menambah data baru yang dicentang dan menghapus centang yang dilepas
        $matkul->cpl()->sync($request->cpl_ids);

        return ApiResponse::success($matkul->load('cpl'), 'Mapping CPL Mata Kuliah berhasil disimpan');
    }
}