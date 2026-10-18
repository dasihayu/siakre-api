<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\StatusMahasiswa;
use Illuminate\Http\Request;

class StatusMahasiswaController extends Controller
{
    public function index()
    {
        $data = StatusMahasiswa::with('enrollment')->get();

        return ApiResponse::success($data, 'Berhasil mengambil data Status Mahasiswa');
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_enrollment_mahasiswa_angkatan_kelas' => 'required|exists:enrollment_mahasiswa_angkatan_kelas,id',
            'status' => 'required|string',
        ]);

        $status = StatusMahasiswa::create($request->all());

        return ApiResponse::success($status, 'Data Status Mahasiswa berhasil ditambahkan', 201);
    }

    public function show(string $id)
    {
        $status = StatusMahasiswa::with('enrollment')->find($id);

        if (! $status) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        return ApiResponse::success($status, 'Berhasil mengambil detail data Status Mahasiswa');
    }

    public function update(Request $request, string $id)
    {
        $status = StatusMahasiswa::find($id);

        if (! $status) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        $request->validate([
            'id_enrollment_mahasiswa_angkatan_kelas' => 'required|exists:enrollment_mahasiswa_angkatan_kelas,id',
            'status' => 'required|string',
        ]);

        $status->update($request->all());

        return ApiResponse::success($status, 'Data Status Mahasiswa berhasil diupdate');
    }

    public function destroy(string $id)
    {
        $status = StatusMahasiswa::find($id);

        if (! $status) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        $status->delete();

        return ApiResponse::success(null, 'Data berhasil dihapus');
    }
}
