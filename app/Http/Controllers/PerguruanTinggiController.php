<?php

namespace App\Http\Controllers;

use App\Models\PerguruanTinggi;
use Illuminate\Http\Request;
use App\Helpers\ApiResponse; 

class PerguruanTinggiController extends Controller
{
    // Tampil semua data (Read)
    public function index()
    {
        $data = PerguruanTinggi::all();
        return ApiResponse::success($data, 'Berhasil mengambil data');
    }

    // Tambah data (Create)
    public function store(Request $request)
    {
        $request->validate([
            'kode_pt' => 'required|string',
            'nama_pt' => 'required|string',
        ]);

        $pt = PerguruanTinggi::create($request->all());
        return ApiResponse::success($pt, 'Data Perguruan Tinggi berhasil ditambahkan', 201);
    }

    // Tampil satu data spesifik (Read Detail)
    public function show(string $id)
    {
        $pt = PerguruanTinggi::find($id);
        if (!$pt) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }
        
        return ApiResponse::success($pt, 'Berhasil mengambil detail data');
    }

    // Update data
    public function update(Request $request, string $id)
    {
        $pt = PerguruanTinggi::find($id);
        if (!$pt) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        $pt->update($request->all());
        return ApiResponse::success($pt, 'Data berhasil diupdate');
    }

    // Hapus data (Delete)
    public function destroy(string $id)
    {
        $pt = PerguruanTinggi::find($id);
        if (!$pt) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        $pt->delete();
        // Karena hapus nggak butuh balikin data, parameter datanya diisi null
        return ApiResponse::success(null, 'Data berhasil dihapus'); 
    }
}