<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use App\Helpers\ApiResponse;

class MahasiswaController extends Controller
{
    public function index()
    {
        $data = Mahasiswa::all();
        return ApiResponse::success($data, 'Berhasil mengambil data Mahasiswa');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nim'                    => 'required|string|unique:data_mahasiswa,nim',
            'nama_mahasiswa'         => 'required|string',
            'tahun_masuk'            => 'required|integer',
            'jenis_pendaftaran'      => 'required|string',
            'mahasiswa_internasional'=> 'required|boolean',
        ], [
            'nim.unique' => 'NIM Mahasiswa Sudah Ada'
        ]);

        $mhs = Mahasiswa::create($request->all());
        
        return ApiResponse::success($mhs, 'Data Mahasiswa berhasil ditambahkan', 201);
    }

    public function show(string $id)
    {
        $mhs = Mahasiswa::find($id);
        
        if (!$mhs) {
            return ApiResponse::error('Data mahasiswa tidak ditemukan', 404);
        }
        
        return ApiResponse::success($mhs, 'Berhasil mengambil detail data Mahasiswa');
    }

    public function update(Request $request, string $id)
    {
        $mhs = Mahasiswa::find($id);
        
        if (!$mhs) {
            return ApiResponse::error('Data mahasiswa tidak ditemukan', 404);
        }

        $request->validate([
            'nim'                    => 'required|string|unique:data_mahasiswa,nim,' . $id . ',id',
            'nama_mahasiswa'         => 'required|string',
            'tahun_masuk'            => 'required|integer',
            'jenis_pendaftaran'      => 'required|string',
            'mahasiswa_internasional'=> 'required|boolean',
        ], [
            'nim.unique' => 'NIM Mahasiswa Sudah Ada'
        ]);

        $mhs->update($request->all());
        
        return ApiResponse::success($mhs, 'Data Mahasiswa berhasil diupdate');
    }

    public function destroy(string $id)
    {
        $mhs = Mahasiswa::find($id);
        
        if (!$mhs) {
            return ApiResponse::error('Data mahasiswa tidak ditemukan', 404);
        }

        try {
            $mhs->delete();
            return ApiResponse::success(null, 'Data mahasiswa berhasil dihapus');
            
        } catch (\Illuminate\Database\QueryException $e) {
            return ApiResponse::error('Gagal menghapus data karena masih terikat relasi', 400);
        }
    }
}
