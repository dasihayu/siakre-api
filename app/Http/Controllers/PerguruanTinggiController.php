<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\PerguruanTinggi\StorePerguruanTinggiRequest;
use App\Http\Requests\PerguruanTinggi\UpdatePerguruanTinggiRequest;
use App\Http\Resources\PerguruanTinggiResource;
use App\Models\PerguruanTinggi;
use App\Models\Upps;

class PerguruanTinggiController extends Controller
{
    /** Tampil semua data (Read) */
    public function index()
    {
        $data = PerguruanTinggiResource::collection(PerguruanTinggi::all());

        return ApiResponse::success($data, 'Berhasil mengambil data');
    }

    /** Tambah data (Create) */
    public function store(StorePerguruanTinggiRequest $request)
    {
        $pt = PerguruanTinggi::create($request->validated());

        return ApiResponse::success(new PerguruanTinggiResource($pt), 'Data Perguruan Tinggi berhasil ditambahkan', 201);
    }

    /** Tampil satu data spesifik (Read Detail) */
    public function show(string $id)
    {
        $pt = PerguruanTinggi::find($id);

        if (! $pt) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        return ApiResponse::success(new PerguruanTinggiResource($pt), 'Berhasil mengambil detail data');
    }

    /** Update data (Sesuai Flowchart: Edit PT) */
    public function update(UpdatePerguruanTinggiRequest $request, string $id)
    {
        $pt = PerguruanTinggi::find($id);

        if (! $pt) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        $pt->update($request->validated());

        return ApiResponse::success(new PerguruanTinggiResource($pt), 'Data berhasil diupdate');
    }

    /** Hapus data (Sesuai Flowchart: Hapus PT) */
    public function destroy(string $id)
    {
        $pt = PerguruanTinggi::find($id);

        if (! $pt) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        // Cek Relasi ke Fakultas / UPPS (Sesuai flowchart)
        $adaDataTerikat = Upps::where('id_pt', $id)->exists();

        if ($adaDataTerikat) {
            return ApiResponse::error('Data PT Masih Digunakan', 400);
        }

        $pt->delete();

        return ApiResponse::success(null, 'Data berhasil dihapus');
    }
}
