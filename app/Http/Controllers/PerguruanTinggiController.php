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

    // Update data (Sesuai Flowchart: Edit PT)
    public function update(Request $request, string $id)
    {
        $pt = PerguruanTinggi::find($id);
        if (!$pt) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        // Cek Kelengkapan Data (Sesuai flowchart)
        $request->validate([
            'kode_pt' => 'required|string',
            'nama_pt' => 'required|string',
        ]);

        $pt->update($request->all());
        
        // Tampilkan Pesan Berhasil (Sesuai flowchart)
        return ApiResponse::success($pt, 'Data berhasil diupdate');
    }

    // Hapus data (Sesuai Flowchart: Hapus PT)
    public function destroy(string $id)
    {
        $pt = PerguruanTinggi::find($id);
        if (!$pt) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        // Cek Relasi ke Fakultas / UPPS (Sesuai flowchart)
        // import model Upps untuk mengecek relasi
        $adaDataTerikat = \App\Models\Upps::where('id_pt', $id)->exists();

        // Ada Data Terikat? Ya
        if ($adaDataTerikat) {
            // Tampilkan Error 'Data PT Masih Digunakan'
            return ApiResponse::error('Data PT Masih Digunakan', 400); 
        }

        // Ada Data Terikat? Tidak -> Hapus Data PT dari Database
        $pt->delete();
        
        // Tampilkan Pesan Berhasil
        return ApiResponse::success(null, 'Data berhasil dihapus'); 
    }
}