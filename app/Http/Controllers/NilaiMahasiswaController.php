<?php

namespace App\Http\Controllers;

use App\Models\NilaiMahasiswa;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;

class NilaiMahasiswaController extends Controller
{
    public function index()
    {
        $nilai = NilaiMahasiswa::all();
        return ApiResponse::success($nilai, 'Daftar nilai mahasiswa berhasil diambil');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'enrollment_mahasiswa_angkatan_kelas_id' => 'required|integer',
            'mata_kuliah_angkatan_kelas_id' => 'required|integer',
            'nilai_angka' => 'required|numeric',
            'nilai_huruf' => 'required|string|max:5',
            'bobot_nilai' => 'required|numeric',
            'status_kelulusan' => 'required|string|max:50',
        ]);

        $nilai = NilaiMahasiswa::create($validated);
        return ApiResponse::success($nilai, 'Nilai mahasiswa berhasil ditambahkan', 201);
    }

    public function show($id)
    {
        $nilai = NilaiMahasiswa::find($id);

        if (!$nilai) {
            return ApiResponse::error('Data nilai mahasiswa tidak ditemukan', 404);
        }

        return ApiResponse::success($nilai, 'Detail data nilai mahasiswa berhasil diambil');
    }

    public function update(Request $request, $id)
    {
        $nilai = NilaiMahasiswa::find($id);

        if (!$nilai) {
            return ApiResponse::error('Data nilai mahasiswa tidak ditemukan', 404);
        }

        $validated = $request->validate([
            'enrollment_mahasiswa_angkatan_kelas_id' => 'sometimes|required|integer',
            'mata_kuliah_angkatan_kelas_id' => 'sometimes|required|integer',
            'nilai_angka' => 'sometimes|required|numeric',
            'nilai_huruf' => 'sometimes|required|string|max:5',
            'bobot_nilai' => 'sometimes|required|numeric',
            'status_kelulusan' => 'sometimes|required|string|max:50',
        ]);

        $nilai->update($validated);
        return ApiResponse::success($nilai, 'Data nilai mahasiswa berhasil diperbarui');
    }

    public function destroy($id)
    {
        $nilai = NilaiMahasiswa::find($id);

        if (!$nilai) {
            return ApiResponse::error('Data nilai mahasiswa tidak ditemukan', 404);
        }

        $nilai->delete();
        return ApiResponse::success(null, 'Data nilai mahasiswa berhasil dihapus');
    }
}