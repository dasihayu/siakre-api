<?php

namespace App\Http\Controllers;

use App\Models\AngkatanKelas;
use Illuminate\Http\Request;
use App\Helpers\ApiResponse;

class AngkatanKelasController extends Controller
{
    public function index()
    {
        $data = AngkatanKelas::with(['kelas', 'angkatan', 'prodi', 'tahunAkademik'])->get();
        return ApiResponse::success($data, 'Berhasil mengambil data Angkatan Kelas');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kelas_id'          => 'required|exists:kelas,id',
            'angkatan_id'       => 'required|exists:angkatan,id',
            'prodi_id'          => 'required|exists:prodi,id',
            'tahun_akademik_id' => 'required|exists:tahun_akademik,id',
        ]);

        $angkatanKelas = AngkatanKelas::create($request->all());
        
        return ApiResponse::success($angkatanKelas, 'Data Angkatan Kelas berhasil ditambahkan', 201);
    }

    public function show(string $id)
    {
        $angkatanKelas = AngkatanKelas::with(['kelas', 'angkatan', 'prodi', 'tahunAkademik'])->find($id);
        
        if (!$angkatanKelas) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }
        
        return ApiResponse::success($angkatanKelas, 'Berhasil mengambil detail data Angkatan Kelas');
    }

    public function update(Request $request, string $id)
    {
        $angkatanKelas = AngkatanKelas::find($id);
        
        if (!$angkatanKelas) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        $request->validate([
            'kelas_id'          => 'required|exists:kelas,id',
            'angkatan_id'       => 'required|exists:angkatan,id',
            'prodi_id'          => 'required|exists:prodi,id',
            'tahun_akademik_id' => 'required|exists:tahun_akademik,id',
        ]);

        $angkatanKelas->update($request->all());
        
        return ApiResponse::success($angkatanKelas, 'Data Angkatan Kelas berhasil diupdate');
    }

    public function destroy(string $id)
    {
        $angkatanKelas = AngkatanKelas::find($id);
        
        if (!$angkatanKelas) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        $angkatanKelas->delete();
        return ApiResponse::success(null, 'Data berhasil dihapus');
    }
}