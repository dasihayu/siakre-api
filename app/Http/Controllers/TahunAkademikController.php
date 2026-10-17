<?php

namespace App\Http\Controllers;

use App\Models\TahunAkademik;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TahunAkademikController extends Controller
{
    public function index(Request $request)
    {
        try {
            $tahunAkademik = $request->has('per_page') ? TahunAkademik::paginate($request->get('per_page', 10)) : TahunAkademik::all();
            return ApiResponse::success($tahunAkademik, 'Berhasil mendapatkan data tahun akademik');
        } catch (\Exception $e) {
            return ApiResponse::error('Gagal memuat data tahun akademik: ' . $e->getMessage(), 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'tahun' => 'required|string|max:20|unique:tahun_akademik,tahun',
                'semester' => 'required|in:Ganjil,Genap',
                'is_active' => 'boolean',
            ]);

            $tahunAkademik = TahunAkademik::create($validated);

            return ApiResponse::success($tahunAkademik, 'Tahun akademik berhasil ditambahkan', 201);
        } catch (ValidationException $e) {
            return ApiResponse::validationError($e->errors(), 'Validasi gagal');
        } catch (\Exception $e) {
            return ApiResponse::error('Gagal menambahkan tahun akademik: ' . $e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        $tahunAkademik = TahunAkademik::find($id);

        if (!$tahunAkademik) {
            return ApiResponse::error('Data tahun akademik tidak ditemukan', 404);
        }

        return ApiResponse::success($tahunAkademik, 'Detail tahun akademik berhasil ditemukan');
    }

    public function update(Request $request, $id)
    {
        $tahunAkademik = TahunAkademik::find($id);

        if (!$tahunAkademik) {
            return ApiResponse::error('Data tahun akademik tidak ditemukan', 404);
        }

        try {
            $validated = $request->validate([
                'tahun' => 'required|string|max:20|unique:tahun_akademik,tahun,' . $id,
                'semester' => 'required|in:Ganjil,Genap',
                'is_active' => 'boolean',
            ]);

            $tahunAkademik->update($validated);

            return ApiResponse::success($tahunAkademik, 'Tahun akademik berhasil diperbarui');
        } catch (ValidationException $e) {
            return ApiResponse::validationError($e->errors(), 'Validasi gagal');
        } catch (\Exception $e) {
            return ApiResponse::error('Gagal memperbarui tahun akademik: ' . $e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        $tahunAkademik = TahunAkademik::find($id);

        if (!$tahunAkademik) {
            return ApiResponse::error('Data tahun akademik tidak ditemukan', 404);
        }

        try {
            $tahunAkademik->delete();
            return ApiResponse::success(null, 'Tahun akademik berhasil dihapus');
        } catch (\Exception $e) {
            return ApiResponse::error('Gagal menghapus tahun akademik: ' . $e->getMessage(), 500);
        }
    }
}