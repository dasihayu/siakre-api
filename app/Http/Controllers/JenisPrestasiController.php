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
        $request->validate([
            'id_prestasi_mahasiswa' => 'required|exists:prestasi_mahasiswa,id',
            'jenis' => 'required|string',
        ]);

        $jenis = JenisPrestasi::create($request->all());

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

        $request->validate([
            'id_prestasi_mahasiswa' => 'required|exists:prestasi_mahasiswa,id',
            'jenis' => 'required|string',
        ]);

        $jenis->update($request->all());

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
