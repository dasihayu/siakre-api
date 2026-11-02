<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\Cpl\StoreCplRequest;
use App\Http\Requests\Cpl\UpdateCplRequest;
use App\Models\Cpl;
use Illuminate\Database\QueryException;

class CplController extends Controller
{
    // Tampil semua data (Read)
    public function index()
    {
        $data = Cpl::with('kurikulum')->get();

        return ApiResponse::success($data, 'Berhasil mengambil data CPL');
    }

    // Tambah data (Sesuai Flowchart: Tambah)
    public function store(StoreCplRequest $request)
    {
        $cpl = Cpl::create($request->validated());

        return ApiResponse::success($cpl->load('kurikulum'), 'Data CPL berhasil ditambahkan', 201);
    }

    // Tampil satu data spesifik
    public function show(string $id)
    {
        $cpl = Cpl::with('kurikulum')->find($id);

        if (! $cpl) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        return ApiResponse::success($cpl, 'Berhasil mengambil detail data CPL');
    }

    // Update data (Sesuai Flowchart: Edit)
    public function update(UpdateCplRequest $request, string $id)
    {
        $cpl = Cpl::find($id);

        if (! $cpl) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        $cpl->update($request->validated());

        return ApiResponse::success($cpl->load('kurikulum'), 'Data CPL berhasil diupdate');
    }

    // Hapus data (Sesuai Flowchart: Hapus)
    public function destroy(string $id)
    {
        $cpl = Cpl::find($id);

        if (! $cpl) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        try {
            // Sesuai flowchart: Hapus CPL dari Database
            $cpl->delete();

            return ApiResponse::success(null, 'Data berhasil dihapus');

        } catch (QueryException $e) {
            // Sesuai flowchart: Jika terikat ke Matkul/Nilai (Error Code: 23000)
            if ($e->getCode() == '23000') {
                return ApiResponse::error('CPL Masih Digunakan', 400);
            }

            return ApiResponse::error('Gagal menghapus data', 500);
        }
    }
}
