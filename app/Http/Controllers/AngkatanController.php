<?php

namespace App\Http\Controllers;

use App\Models\Angkatan;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;

class AngkatanController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Mendukung paginasi jika ada parameter ?per_page=10, atau tampilkan semua
            if ($request->has('per_page') || $request->has('page')) {
                $perPage = $request->get('per_page', 10);
                $angkatan = Angkatan::paginate($perPage);
            } else {
                $angkatan = Angkatan::all();
            }

            return ApiResponse::success($angkatan, 'Berhasil mendapatkan data angkatan');
        } catch (\Exception $e) {
            return ApiResponse::error('Gagal memuat data angkatan: ' . $e->getMessage(), 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
            'angkatan' => 'required|integer|unique:angkatan,angkatan',
            ]);
            
            $angkatan = Angkatan::create($validated);

            return ApiResponse::success($angkatan, 'Angkatan berhasil ditambahkan', 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResponse::validationError($e->errors(), 'Validasi gagal');
        } catch (\Exception $e) {
            return ApiResponse::error('Gagal menambahkan angkatan: ' . $e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        $angkatan = Angkatan::find($id);

        if (!$angkatan) {
            return ApiResponse::error('Data angkatan tidak ditemukan', 404);
        }

        return ApiResponse::success($angkatan, 'Detail angkatan berhasil ditemukan');
    }

    public function update(Request $request, $id)
    {
        $angkatan = Angkatan::find($id);

        if (!$angkatan) {
            return ApiResponse::error('Data angkatan tidak ditemukan', 404);
        }

        try {
            $validated = $request->validate([
                'tahun' => 'required|string|max:10|unique:angkatans,tahun,' . $id,
            ]);

            $angkatan->update($validated);

            return ApiResponse::success($angkatan, 'Angkatan berhasil diperbarui');
        } catch (ValidationException $e) {
            return ApiResponse::validationError($e->errors(), 'Validasi gagal');
        } catch (\Exception $e) {
            return ApiResponse::error('Gagal memperbarui angkatan: ' . $e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        $angkatan = Angkatan::find($id);

        if (!$angkatan) {
            return ApiResponse::error('Data angkatan tidak ditemukan', 404);
        }

        try {
            $angkatan->delete();
            return ApiResponse::success(null, 'Angkatan berhasil dihapus');
        } catch (\Exception $e) {
            return ApiResponse::error('Gagal menghapus angkatan: ' . $e->getMessage(), 500);
        }
    }
}