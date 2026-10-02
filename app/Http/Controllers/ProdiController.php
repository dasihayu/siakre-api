<?php

namespace App\Http\Controllers;

use App\Models\Prodi;
use Illuminate\Http\Request;
use App\Helpers\ApiResponse;

class ProdiController extends Controller
{
    public function index()
    {
        $data = Prodi::all();
        return ApiResponse::success($data, 'Berhasil mengambil data Prodi');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_prodi' => 'required|string',
            'nama_prodi' => 'required|string',
        ]);

        $prodi = Prodi::create($request->all());
        return ApiResponse::success($prodi, 'Data Prodi berhasil ditambahkan', 201);
    }

    public function show(string $id)
    {
        $prodi = Prodi::find($id);
        if (!$prodi) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }
        
        return ApiResponse::success($prodi, 'Berhasil mengambil detail data Prodi');
    }

    public function update(Request $request, string $id)
    {
        $prodi = Prodi::find($id);
        if (!$prodi) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        $prodi->update($request->all());
        return ApiResponse::success($prodi, 'Data Prodi berhasil diupdate');
    }

    public function destroy(string $id)
    {
        $prodi = Prodi::find($id);
        if (!$prodi) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        $prodi->delete();
        return ApiResponse::success(null, 'Data Prodi berhasil dihapus');
    }
}