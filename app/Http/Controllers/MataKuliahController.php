<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\MataKuliah;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class MataKuliahController extends Controller
{
    public function index()
    {
        $data = MataKuliah::with('kurikulum')->get();

        return ApiResponse::success($data, 'Berhasil mengambil data Mata Kuliah');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_kurikulum' => 'required|exists:kurikulum,id',
            'kode_mata_kuliah' => 'required|string|unique:mata_kuliah,kode_mata_kuliah',
            'nama_mata_kuliah' => 'required|string',
            'sks' => 'required|integer|min:0',
            'semester' => 'nullable|integer|between:1,14',
            'jenis' => 'nullable|in:Wajib,Pilihan',
            'sks_teori' => 'nullable|integer|min:0',
            'sks_praktik' => 'nullable|integer|min:0',
        ], [
            'kode_mata_kuliah.unique' => 'Kode Mata Kuliah Sudah Ada',
        ]);

        $matkul = MataKuliah::create($validated);

        return ApiResponse::success($matkul, 'Data Mata Kuliah berhasil ditambahkan', 201);
    }

    public function show(string $id)
    {
        $matkul = MataKuliah::with('kurikulum')->find($id);

        if (! $matkul) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        return ApiResponse::success($matkul, 'Berhasil mengambil detail data Mata Kuliah');
    }

    public function update(Request $request, string $id)
    {
        $matkul = MataKuliah::find($id);

        if (! $matkul) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        $validated = $request->validate([
            'id_kurikulum' => 'required|exists:kurikulum,id',
            'kode_mata_kuliah' => 'required|string|unique:mata_kuliah,kode_mata_kuliah,'.$id,
            'nama_mata_kuliah' => 'required|string',
            'sks' => 'required|integer|min:0',
            'semester' => 'nullable|integer|between:1,14',
            'jenis' => 'nullable|in:Wajib,Pilihan',
            'sks_teori' => 'nullable|integer|min:0',
            'sks_praktik' => 'nullable|integer|min:0',
        ], [
            'kode_mata_kuliah.unique' => 'Kode Mata Kuliah Sudah Ada',
        ]);

        $matkul->update($validated);

        return ApiResponse::success($matkul, 'Data Mata Kuliah berhasil diupdate');
    }

    public function destroy(string $id)
    {
        $matkul = MataKuliah::find($id);

        if (! $matkul) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        try {
            $matkul->delete();

            return ApiResponse::success(null, 'Data berhasil dihapus');

        } catch (QueryException $e) {
            // Error 23000 muncul saat dicoba dihapus tapi data masih nyangkut di tabel nilai / cpl
            if ($e->getCode() == '23000') {
                return ApiResponse::error('Matkul Masih Digunakan', 400);
            }

            return ApiResponse::error('Gagal menghapus data', 500);
        }
    }
}
