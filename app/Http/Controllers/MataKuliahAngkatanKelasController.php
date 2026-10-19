<?php

namespace App\Http\Controllers;

use App\Models\MataKuliahAngkatanKelas;
use Illuminate\Http\Request;
use App\Helpers\ApiResponse;

class MataKuliahAngkatanKelasController extends Controller
{
    public function index()
    {
        $data = MataKuliahAngkatanKelas::with(['enrollmentMataKuliah', 'angkatanKelas'])->get();
        return ApiResponse::success($data, 'Berhasil mengambil data Mata Kuliah Angkatan Kelas');
    }

    public function store(Request $request)
    {
        $request->validate([
            'enrollment_mata_kuliah_id' => 'required|exists:enrollment_mata_kuliah,id',
            'angkatan_kelas_id'         => 'required|exists:angkatan_kelas,id',
        ]);

        $data = MataKuliahAngkatanKelas::create($request->all());
        return ApiResponse::success($data, 'Berhasil menambahkan data', 201);
    }

    public function show(string $id)
    {
        $data = MataKuliahAngkatanKelas::with(['enrollmentMataKuliah', 'angkatanKelas'])->find($id);
        return $data ? ApiResponse::success($data, 'Berhasil mengambil detail data') : ApiResponse::error('Data tidak ditemukan', 404);
    }

    public function update(Request $request, string $id)
    {
        $data = MataKuliahAngkatanKelas::find($id);
        if (!$data) return ApiResponse::error('Data tidak ditemukan', 404);

        $request->validate([
            'enrollment_mata_kuliah_id' => 'required|exists:enrollment_mata_kuliah,id',
            'angkatan_kelas_id'         => 'required|exists:angkatan_kelas,id',
        ]);

        $data->update($request->all());
        return ApiResponse::success($data, 'Berhasil mengupdate data');
    }

    public function destroy(string $id)
    {
        $data = MataKuliahAngkatanKelas::find($id);
        if (!$data) return ApiResponse::error('Data tidak ditemukan', 404);
        
        $data->delete();
        return ApiResponse::success(null, 'Berhasil menghapus data');
    }
}