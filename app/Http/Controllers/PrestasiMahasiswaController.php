<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\PrestasiMahasiswa;
use Illuminate\Http\Request;

class PrestasiMahasiswaController extends Controller
{
    // Tampil semua data (Read)
    public function index()
    {
        $data = PrestasiMahasiswa::with(['mahasiswa'])->get();

        return ApiResponse::success($data, 'Berhasil mengambil data Prestasi Mahasiswa');
    }

    // Tambah data (Sesuai Flowchart: Tambah)
    public function store(Request $request)
    {
        $request->validate([
            'id_mahasiswa' => 'required|exists:data_mahasiswa,id',
            'nama_aktivitas' => 'required|string',
            'tahun' => 'required|integer',
        ]);

        $prestasi = PrestasiMahasiswa::create($request->all());

        // Tampilkan Pesan Berhasil
        return ApiResponse::success($prestasi, 'Data Prestasi Mahasiswa berhasil ditambahkan', 201);
    }

    // Tampil satu data spesifik
    public function show(string $id)
    {
        $prestasi = PrestasiMahasiswa::with(['mahasiswa'])->find($id);

        if (! $prestasi) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        return ApiResponse::success($prestasi, 'Berhasil mengambil detail data Prestasi Mahasiswa');
    }

    // Update data (Sesuai Flowchart: Edit)
    public function update(Request $request, string $id)
    {
        $prestasi = PrestasiMahasiswa::find($id);

        if (! $prestasi) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        $request->validate([
            'id_mahasiswa' => 'required|exists:data_mahasiswa,id',
            'nama_aktivitas' => 'required|string',
            'tahun' => 'required|integer',
        ]);

        $prestasi->update($request->all());

        // Tampilkan Pesan Berhasil
        return ApiResponse::success($prestasi, 'Data Prestasi Mahasiswa berhasil diupdate');
    }

    // Hapus data (Sesuai Flowchart: Hapus)
    public function destroy(string $id)
    {
        $prestasi = PrestasiMahasiswa::find($id);

        if (! $prestasi) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        $prestasi->delete();

        // Tampilkan Pesan Berhasil
        return ApiResponse::success(null, 'Data berhasil dihapus');
    }
}
