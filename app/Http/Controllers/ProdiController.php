<?php

namespace App\Http\Controllers;

use App\Models\Prodi;
use App\Models\Upps;
use Illuminate\Http\Request;
use App\Helpers\ApiResponse;
use Illuminate\Support\Facades\DB; // untuk mengecek tabel yang mungkin belum ada Model-nya

class ProdiController extends Controller
{
    // Tampil semua data
    public function index()
    {
        $data = Prodi::all();
        return ApiResponse::success($data, 'Berhasil mengambil data Prodi');
    }

    // Tambah data (Sesuai Flowchart: Tambah)
    public function store(Request $request)
    {
        // Cek Kelengkapan Data sesuai flowchart
        $request->validate([
            'kode_prodi' => 'required|string|unique:prodi,kode_prodi',
            'nama_prodi' => 'required|string',
            'akreditasi' => 'required|string',
        ]);

        $prodi = Prodi::create($request->all());
        
        // Simpan Prodi Baru ke Database & Tampilkan Pesan Berhasil
        return ApiResponse::success($prodi, 'Data Prodi berhasil ditambahkan', 201);
    }

    // Tampil satu data spesifik
    public function show(string $id)
    {
        $prodi = Prodi::find($id);
        
        if (!$prodi) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }
        
        return ApiResponse::success($prodi, 'Berhasil mengambil detail data Prodi');
    }

    // Update data (Sesuai Flowchart: Edit)
    public function update(Request $request, string $id)
    {
        $prodi = Prodi::find($id);
        
        if (!$prodi) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        // Cek Kelengkapan Data
        $request->validate([
            'kode_prodi' => 'required|string|unique:prodi,kode_prodi,' . $id,
            'nama_prodi' => 'required|string',
            'akreditasi' => 'required|string',
        ]);

        $prodi->update($request->all());
        
        // Update Data Prodi di Database & Tampilkan Pesan Berhasil
        return ApiResponse::success($prodi, 'Data Prodi berhasil diupdate');
    }

    // Hapus data (Sesuai Flowchart: Hapus)
    public function destroy(string $id)
    {
        $prodi = Prodi::find($id);
        
        if (!$prodi) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        // Cek Keterikatan Data Kurikulum, UPPS / Mahasiswa
        // 1. Cek di tabel UPPS 
        $terikatUpps = Upps::where('prodi_id', $id)->exists();

        if ($terikatUpps) {
            return ApiResponse::error('Prodi Masih Digunakan', 400);
        }

        $prodi->delete();

        // Tampilkan Pesan Berhasil
        return ApiResponse::success(null, 'Data berhasil dihapus');
    }
}