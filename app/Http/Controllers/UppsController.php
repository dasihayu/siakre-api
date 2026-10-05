<?php

namespace App\Http\Controllers;

use App\Models\Upps;
use Illuminate\Http\Request;
use App\Helpers\ApiResponse;

class UppsController extends Controller
{
    // Tampil semua data (Read)
    public function index()
    {
        $data = Upps::with(['perguruanTinggi', 'prodi'])->get();
        return ApiResponse::success($data, 'Berhasil mengambil data UPPS');
    }

    // Tambah data (Sesuai Flowchart: Tambah)
    public function store(Request $request)
    {
        $request->validate([
            'id_pt'         => 'required|exists:perguruan_tinggis,id',
            // Cek Kode Duplikat sesuai flowchart
            'kode_upps'     => 'required|string|unique:upps,kode_upps', 
            'nama_upps'     => 'required|string',
            'pimpinan_upps' => 'nullable|string',
            'prodi_id'      => 'required|exists:prodi,id',
            'jenis'         => 'required|string',
        ], [
            'kode_upps.unique' => 'Kode UPPS Sudah Ada' // Pesan error spesifik
        ]);

        $upps = Upps::create($request->all());
        
        // Tampilkan Pesan Berhasil
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

    // Update data (Sesuai Flowchart: Edit)
    public function update(Request $request, string $id)
    {
        $upps = Upps::find($id);
        
        if (!$upps) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        $request->validate([
            'id_pt'         => 'required|exists:perguruan_tinggis,id',
            'kode_upps'     => 'required|string|unique:upps,kode_upps,' . $id,
            'nama_upps'     => 'required|string',
            'pimpinan_upps' => 'nullable|string',
            'prodi_id'      => 'required|exists:prodi,id',
            'jenis'         => 'required|string',
        ], [
            'kode_upps.unique' => 'Kode UPPS Sudah Ada'
        ]);

        $upps->update($request->all());
        
        // Tampilkan Pesan Berhasil
        return ApiResponse::success($upps, 'Data UPPS berhasil diupdate');
    }

    // Hapus data (Sesuai Flowchart: Hapus)
    public function destroy(string $id)
    {
        $upps = Upps::find($id);
        
        if (!$upps) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        // Langsung eksekusi hapus sesuai flowchart Master UPPS terbaru
        $upps->delete();
        
        // Tampilkan Pesan Berhasil
        return ApiResponse::success(null, 'Data berhasil dihapus');
    }
}