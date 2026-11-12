<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\Upps;
use Illuminate\Http\Request;

class UppsController extends Controller
{
    // Tampil semua data (Read)
    public function index()
    {
        $data = Upps::with(['perguruanTinggi', 'prodis'])->get();

        return ApiResponse::success($data, 'Berhasil mengambil data UPPS');
    }

    // Tambah data (Sesuai Flowchart: Tambah)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_pt' => 'required|exists:perguruan_tinggis,id',
            // Cek Kode Duplikat sesuai flowchart
            'kode_upps' => 'required|string|unique:upps,kode_upps',
            'nama_upps' => 'required|string',
            'pimpinan_upps' => 'nullable|string',
            'jenis' => 'required|string',
        ], [
            'kode_upps.unique' => 'Kode UPPS Sudah Ada', // Pesan error spesifik
        ]);

        $upps = Upps::create($validated);

        // Tampilkan Pesan Berhasil
        return ApiResponse::success($upps, 'Data UPPS berhasil ditambahkan', 201);
    }

    // Tampil satu data spesifik
    public function show(string $id)
    {
        $upps = Upps::with(['perguruanTinggi', 'prodis'])->find($id);

        if (! $upps) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        return ApiResponse::success($upps, 'Berhasil mengambil detail data UPPS');
    }

    // Update data (Sesuai Flowchart: Edit)
    public function update(Request $request, string $id)
    {
        $upps = Upps::find($id);

        if (! $upps) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        $validated = $request->validate([
            'id_pt' => 'required|exists:perguruan_tinggis,id',
            'kode_upps' => 'required|string|unique:upps,kode_upps,'.$id,
            'nama_upps' => 'required|string',
            'pimpinan_upps' => 'nullable|string',
            'jenis' => 'required|string',
        ], [
            'kode_upps.unique' => 'Kode UPPS Sudah Ada',
        ]);

        $upps->update($validated);

        // Tampilkan Pesan Berhasil
        return ApiResponse::success($upps, 'Data UPPS berhasil diupdate');
    }

    // Hapus data (Sesuai Flowchart: Hapus)
    public function destroy(string $id)
    {
        $upps = Upps::find($id);

        if (! $upps) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        if ($upps->prodis()->exists()) {
            return ApiResponse::error('UPPS masih memiliki program studi', 400);
        }

        // Langsung eksekusi hapus sesuai flowchart Master UPPS terbaru
        $upps->delete();

        // Tampilkan Pesan Berhasil
        return ApiResponse::success(null, 'Data berhasil dihapus');
    }
}
