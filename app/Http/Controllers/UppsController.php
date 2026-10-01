<?php

namespace App\Http\Controllers;

use App\Models\Upps;
use Illuminate\Http\Request;
use App\Helpers\ApiResponse;

class UppsController extends Controller
{
    // Tampil semua data (beserta relasinya)
    public function index()
    {
        // with() digunakan untuk memanggil data relasi secara langsung (Eager Loading)
        $data = Upps::with(['perguruanTinggi', 'prodi'])->get();
        return ApiResponse::success($data, 'Berhasil mengambil data UPPS');
    }

    // Tambah data
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'id_pt'         => 'required|exists:perguruan_tinggis,id', // Pastikan ID ada di tabel perguruan_tinggis
            'kode_upps'     => 'required|string',
            'nama_upps'     => 'required|string',
            'pimpinan_upps' => 'nullable|string',
            'prodi_id'      => 'required|exists:prodi,id', // Pastikan ID ada di tabel prodi
            'jenis'         => 'required|string',
        ]);

        $upps = Upps::create($request->all());
        return ApiResponse::success($upps, 'Data UPPS berhasil ditambahkan', 201);
    }

    // Tampil satu data spesifik
    public function show(string $id)
    {
        $upps = Upps::with(['perguruanTinggi', 'prodi'])->find($id);
        
        if (!$upps) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }
        
        return ApiResponse::success($upps, 'Berhasil mengambil detail data UPPS');
    }

    // Update data
    public function update(Request $request, string $id)
    {
        $upps = Upps::find($id);
        
        if (!$upps) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        $request->validate([
            'id_pt'         => 'required|exists:perguruan_tinggis,id',
            'kode_upps'     => 'required|string',
            'nama_upps'     => 'required|string',
            'pimpinan_upps' => 'nullable|string',
            'prodi_id'      => 'required|exists:prodi,id',
            'jenis'         => 'required|string',
        ]);

        $upps->update($request->all());
        return ApiResponse::success($upps, 'Data UPPS berhasil diupdate');
    }

    // Hapus data
    public function destroy(string $id)
    {
        $upps = Upps::find($id);
        
        if (!$upps) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        $upps->delete();
        return ApiResponse::success(null, 'Data UPPS berhasil dihapus');
    }
}