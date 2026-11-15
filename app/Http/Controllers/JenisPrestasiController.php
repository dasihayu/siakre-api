<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\JenisPrestasi;
use Illuminate\Http\Request;

class JenisPrestasiController extends Controller
{
// Tampil semua data (Read)
    public function index()
    {
        $data = JenisPrestasi::with(['prestasiMahasiswa'])->get();

        return ApiResponse::success($data, 'Berhasil mengambil data Jenis Prestasi');
    }

    // Tambah data (Sesuai Flowchart: Tambah)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode' => 'required|string|unique:jenis_prestasi,kode',
            'nama' => 'required|string|max:255',
            'kategori' => 'required|in:Akademik,Non-Akademik',
            'tingkat' => 'required|in:Internasional,Nasional,Regional,Lokal',
            'is_active' => 'boolean',
        ]);

        $jenis = JenisPrestasi::create($validated);

        // Tampilkan Pesan Berhasil
        return ApiResponse::success($jenis, 'Data Jenis Prestasi berhasil ditambahkan', 201);
    }

// Tampil satu data spesifik
    public function show(string $id)
    {
        $jenis = JenisPrestasi::with(['prestasiMahasiswa'])->find($id);

        if (! $jenis) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        return ApiResponse::success($jenis, 'Berhasil mengambil detail data Jenis Prestasi');
    }

    // Update data (Sesuai Flowchart: Edit)
    public function update(Request $request, string $id)
    {
        $jenis = JenisPrestasi::find($id);

        if (! $jenis) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        $validated = $request->validate([
            'kode' => 'required|string|unique:jenis_prestasi,kode,' . $id,
            'nama' => 'required|string|max:255',
            'kategori' => 'required|in:Akademik,Non-Akademik',
            'tingkat' => 'required|in:Internasional,Nasional,Regional,Lokal',
            'is_active' => 'boolean',
        ]);

        $jenis->update($validated);

        // Tampilkan Pesan Berhasil
        return ApiResponse::success($jenis, 'Data Jenis Prestasi berhasil diupdate');
    }

    // Hapus data (Sesuai Flowchart: Hapus)
    public function destroy(string $id)
    {
        $jenis = JenisPrestasi::find($id);

        if (! $jenis) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        $jenis->delete();

        // Tampilkan Pesan Berhasil
        return ApiResponse::success(null, 'Data berhasil dihapus');
    }
}