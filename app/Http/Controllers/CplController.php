<?php

namespace App\Http\Controllers;

use App\Models\Cpl;
use Illuminate\Http\Request;
use App\Helpers\ApiResponse;

class CplController extends Controller
{
    public function index()
    {
        $data = Cpl::with('kurikulum')->get();
        return ApiResponse::success($data, 'Berhasil mengambil data CPL');
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_kurikulum'  => 'required|exists:kurikulum,id',
            // Rule unique: tabel cpl, kolom kode_cpl (Sesuai Flowchart H -> I)
            'kode_cpl'      => 'required|string|unique:cpl,kode_cpl', 
            'deskripsi_cpl' => 'required|string',
            'kategori'      => 'nullable|string',
        ], [
            // Kustomisasi pesan error biar sesuai flowchart J
            'kode_cpl.unique' => 'Kode CPL Sudah Ada'
        ]);

        $cpl = Cpl::create($request->all());
        
        return ApiResponse::success($cpl, 'Data CPL berhasil ditambahkan', 201);
    }

    public function show(string $id)
    {
        $cpl = Cpl::with('kurikulum')->find($id);
        
        if (!$cpl) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }
        
        return ApiResponse::success($cpl, 'Berhasil mengambil detail data CPL');
    }

    public function update(Request $request, string $id)
    {
        $cpl = Cpl::find($id);
        
        if (!$cpl) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        $request->validate([
            'id_kurikulum'  => 'required|exists:kurikulum,id',
            // Pengecualian unique untuk ID yang sedang diupdate agar tidak error saat save data diri sendiri
            'kode_cpl'      => 'required|string|unique:cpl,kode_cpl,' . $id, 
            'deskripsi_cpl' => 'required|string',
            'kategori'      => 'nullable|string',
        ], [
            'kode_cpl.unique' => 'Kode CPL Sudah Ada'
        ]);

        $cpl->update($request->all());
        
        return ApiResponse::success($cpl, 'Data CPL berhasil diupdate');
    }

    public function destroy(string $id)
    {
        $cpl = Cpl::find($id);
        
        if (!$cpl) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        try {
            // Sesuai flowchart: Hapus CPL dari Database
            $cpl->delete();
            return ApiResponse::success(null, 'Data berhasil dihapus');
            
        } catch (\Illuminate\Database\QueryException $e) {
            // Sesuai flowchart: Jika terikat ke Matkul/Nilai (Error Code: 23000)
            if ($e->getCode() == "23000") {
                return ApiResponse::error('CPL Masih Digunakan', 400);
            }
            
            return ApiResponse::error('Gagal menghapus data', 500);
        }
    }
}