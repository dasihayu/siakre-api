<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\EvidencePrestasiMahasiswa;
use Illuminate\Http\Request;

class EvidencePrestasiMahasiswaController extends Controller
{
    // Tampil semua data (Read)
    public function index()
    {
        $data = EvidencePrestasiMahasiswa::with(['prestasiMahasiswa'])->get();

        return ApiResponse::success($data, 'Berhasil mengambil data Evidence Prestasi Mahasiswa');
    }

    // Tambah data (Sesuai Flowchart: Tambah)
    public function store(Request $request)
    {
        $request->validate([
            'id_prestasi_mahasiswa' => 'required|exists:prestasi_mahasiswa,id',
            'evidence' => 'required|string',
        ]);

        $evidence = EvidencePrestasiMahasiswa::create($request->all());

        // Tampilkan Pesan Berhasil
        return ApiResponse::success($evidence, 'Data Evidence Prestasi Mahasiswa berhasil ditambahkan', 201);
    }

    // Tampil satu data spesifik
    public function show(string $id)
    {
        $evidence = EvidencePrestasiMahasiswa::with(['prestasiMahasiswa'])->find($id);

        if (! $evidence) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        return ApiResponse::success($evidence, 'Berhasil mengambil detail data Evidence Prestasi Mahasiswa');
    }

    // Update data (Sesuai Flowchart: Edit)
    public function update(Request $request, string $id)
    {
        $evidence = EvidencePrestasiMahasiswa::find($id);

        if (! $evidence) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        $request->validate([
            'id_prestasi_mahasiswa' => 'required|exists:prestasi_mahasiswa,id',
            'evidence' => 'required|string',
        ]);

        $evidence->update($request->all());

        // Tampilkan Pesan Berhasil
        return ApiResponse::success($evidence, 'Data Evidence Prestasi Mahasiswa berhasil diupdate');
    }

    // Hapus data (Sesuai Flowchart: Hapus)
    public function destroy(string $id)
    {
        $evidence = EvidencePrestasiMahasiswa::find($id);

        if (! $evidence) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        $evidence->delete();

        // Tampilkan Pesan Berhasil
        return ApiResponse::success(null, 'Data berhasil dihapus');
    }
}
