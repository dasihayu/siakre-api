<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\Prodi\StoreProdiRequest;
use App\Http\Requests\Prodi\UpdateProdiRequest;
use App\Models\Prodi;
use Illuminate\Database\QueryException;

class ProdiController extends Controller
{
    // Tampil semua data (Read)
    public function index()
    {
        $data = Prodi::with('upps')->get();

        return ApiResponse::success($data, 'Berhasil mengambil data Prodi');
    }

    // Tambah data (Sesuai Flowchart: Tambah)
    public function store(StoreProdiRequest $request)
    {
        $prodi = Prodi::create($request->validated());

        // Simpan Prodi Baru ke Database & Tampilkan Pesan Berhasil
        return ApiResponse::success($prodi->load('upps'), 'Data Prodi berhasil ditambahkan', 201);
    }

    // Tampil satu data spesifik
    public function show(string $id)
    {
        $prodi = Prodi::with('upps')->find($id);

        if (! $prodi) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        return ApiResponse::success($prodi, 'Berhasil mengambil detail data Prodi');
    }

    // Update data (Sesuai Flowchart: Edit)
    public function update(UpdateProdiRequest $request, string $id)
    {
        $prodi = Prodi::find($id);

        if (! $prodi) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        $prodi->update($request->validated());

        // Update Data Prodi di Database & Tampilkan Pesan Berhasil
        return ApiResponse::success($prodi->load('upps'), 'Data Prodi berhasil diupdate');
    }

    // Hapus data (Sesuai Flowchart: Hapus)
    public function destroy(string $id)
    {
        $prodi = Prodi::find($id);

        if (! $prodi) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        try {
            // Langsung coba hapus
            $prodi->delete();

            return ApiResponse::success(null, 'Data berhasil dihapus');

        } catch (QueryException $e) {
            // Tangkap error jika ditolak oleh sistem database (terikat foreign key)
            // Code 23000 adalah kode standar database untuk Integrity constraint violation
            if ($e->getCode() == '23000') {
                return ApiResponse::error('Prodi Masih Digunakan', 400);
            }

            // Jika ada error database lain
            return ApiResponse::error('Gagal menghapus data', 500);
        }
    }
}
