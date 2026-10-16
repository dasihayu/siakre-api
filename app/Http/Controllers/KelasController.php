<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Helpers\ApiResponse;
use Illuminate\Validation\ValidationException;

class KelasController extends Controller
{
    public function index(Request $request)
    {
        try {
            $kelas = $request->has('per_page') ? Kelas::paginate($request->get('per_page', 10)) : Kelas::all();
            return ApiResponse::success($kelas, 'Berhasil mendapatkan data kelas');
        } catch (\Exception $e) {
            return ApiResponse::error('Gagal memuat data kelas: ' . $e->getMessage(), 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'kelas' => 'required|string|max:50|unique:kelas,kelas',
            ]);

            $kelas = Kelas::create($validated);

            return ApiResponse::success($kelas, 'Kelas berhasil ditambahkan', 201);
        } catch (ValidationException $e) {
            return ApiResponse::validationError($e->errors(), 'Validasi gagal');
        } catch (\Exception $e) {
            return ApiResponse::error('Gagal menambahkan kelas: ' . $e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        $kelas = Kelas::find($id);

        if (!$kelas) {
            return ApiResponse::error('Data kelas tidak ditemukan', 404);
        }

        return ApiResponse::success($kelas, 'Detail kelas berhasil ditemukan');
    }

    public function update(Request $request, $id)
    {
        $kelas = Kelas::find($id);

        if (!$kelas) {
            return ApiResponse::error('Data kelas tidak ditemukan', 404);
        }

        try {
            $validated = $request->validate([
                'kelas' => 'required|string|max:50|unique:kelas,kelas,' . $id,
            ]);

            $kelas->update($validated);

            return ApiResponse::success($kelas, 'Kelas berhasil diperbarui');
        } catch (ValidationException $e) {
            return ApiResponse::validationError($e->errors(), 'Validasi gagal');
        } catch (\Exception $e) {
            return ApiResponse::error('Gagal memperbarui kelas: ' . $e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        $kelas = Kelas::find($id);

        if (!$kelas) {
            return ApiResponse::error('Data kelas tidak ditemukan', 404);
        }

        try {
            $kelas->delete();
            return ApiResponse::success(null, 'Kelas berhasil dihapus');
        } catch (\Exception $e) {
            return ApiResponse::error('Gagal menghapus kelas: ' . $e->getMessage(), 500);
        }
    }
}