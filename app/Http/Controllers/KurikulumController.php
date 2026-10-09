<?php

namespace App\Http\Controllers;

use App\Models\Kurikulum;
use Illuminate\Http\Request;
use App\Helpers\ApiResponse;

class KurikulumController extends Controller
{
    // Tampil semua data
    public function index()
    {
        // Pakai with('prodi') biar nama prodinya ikut tampil, bukan cuma ID-nya doang
        $data = Kurikulum::with('prodi')->get();
        return ApiResponse::success($data, 'Berhasil mengambil data Kurikulum');
    }

    // Tambah data (Sesuai Flowchart: Input Nama Kurikulum & Tahun -> Simpan)
    public function store(Request $request)
    {
        $request->validate([
            'id_prodi'       => 'required|exists:prodi,id',
            'nama_kurikulum' => 'required|string',
            'berlaku_sampai' => 'required|integer',
            'sk_kurikulum'   => 'nullable|string',
        ]);

        $kurikulum = Kurikulum::create($request->all());
        
        return ApiResponse::success($kurikulum, 'Data Kurikulum berhasil ditambahkan', 201);
    }

    // Tampil satu data spesifik
    public function show(string $id)
    {
        $kurikulum = Kurikulum::with('prodi')->find($id);
        
        if (!$kurikulum) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }
        
        return ApiResponse::success($kurikulum, 'Berhasil mengambil detail data Kurikulum');
    }

    // Update data (Sesuai Flowchart: Ubah Data / SK Kurikulum -> Update)
    public function update(Request $request, string $id)
    {
        $kurikulum = Kurikulum::find($id);
        
        if (!$kurikulum) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        $request->validate([
            'id_prodi'       => 'required|exists:prodi,id',
            'nama_kurikulum' => 'required|string',
            'berlaku_sampai' => 'required|integer',
            'sk_kurikulum'   => 'nullable|string',
        ]);

        $kurikulum->update($request->all());
        
        return ApiResponse::success($kurikulum, 'Data Kurikulum berhasil diupdate');
    }

    // Hapus data (Sesuai Flowchart: Cek Relasi ke Mata Kuliah -> Hapus)
    public function destroy(string $id)
    {
        $kurikulum = Kurikulum::find($id);
        
        if (!$kurikulum) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        try {
            // Langsung eksekusi hapus. Biarkan database yang mengecek relasi ke CPL/Matkul
            $kurikulum->delete();
            return ApiResponse::success(null, 'Data berhasil dihapus');
            
        } catch (\Illuminate\Database\QueryException $e) {
            // Jika database menolak karena data id_kurikulum masih dipakai di tabel Matkul / CPL
            if ($e->getCode() == "23000") {
                return ApiResponse::error('Kurikulum Memiliki Matkul/CPL', 400);
            }
            
            return ApiResponse::error('Gagal menghapus data', 500);
        }
    }
}