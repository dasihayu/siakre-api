<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TahunAkademikController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = TahunAkademik::query();

            if ($request->has('semester')) {
                $query->where('semester', $request->query('semester'));
            }

            if ($request->has('tahun')) {
                $query->where('tahun', $request->query('tahun'));
            }

            if ($request->has('is_active')) {
                $query->where('is_active', filter_var($request->query('is_active'), FILTER_VALIDATE_BOOLEAN));
            }

            $tahunAkademik = $request->has('per_page')
                ? $query->paginate($request->get('per_page', 10))
                : $query->get();

            return ApiResponse::success($tahunAkademik, 'Berhasil mendapatkan data tahun akademik');
        } catch (\Exception $e) {
            return ApiResponse::error('Gagal memuat data tahun akademik: '.$e->getMessage(), 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $semester = $request->input('semester');

            $validated = $request->validate([
                'tahun' => [
                    'required',
                    'string',
                    'max:20',
                    Rule::unique('tahun_akademik', 'tahun')->where(function ($query) use ($semester) {
                        return $query->where('semester', $semester);
                    }),
                ],
                'semester' => 'required|in:Ganjil,Genap',
                'is_active' => 'boolean',
            ]);

            $tahunAkademik = TahunAkademik::create($validated);

            return ApiResponse::success($tahunAkademik, 'Tahun akademik berhasil ditambahkan', 201);
        } catch (ValidationException $e) {
            return ApiResponse::validationError($e->errors(), 'Validasi gagal');
        } catch (\Exception $e) {
            return ApiResponse::error('Gagal menambahkan tahun akademik: '.$e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        $tahunAkademik = TahunAkademik::find($id);

        if (! $tahunAkademik) {
            return ApiResponse::error('Data tahun akademik tidak ditemukan', 404);
        }

        return ApiResponse::success($tahunAkademik, 'Detail tahun akademik berhasil ditemukan');
    }

    public function update(Request $request, $id)
    {
        $tahunAkademik = TahunAkademik::find($id);

        if (! $tahunAkademik) {
            return ApiResponse::error('Data tahun akademik tidak ditemukan', 404);
        }

        try {
            $semester = $request->input('semester', $tahunAkademik->semester);

            $validated = $request->validate([
                'tahun' => [
                    'required',
                    'string',
                    'max:20',
                    Rule::unique('tahun_akademik', 'tahun')->where(function ($query) use ($semester) {
                        return $query->where('semester', $semester);
                    })->ignore($id),
                ],
                'semester' => 'required|in:Ganjil,Genap',
                'is_active' => 'boolean',
            ]);

            $tahunAkademik->update($validated);

            return ApiResponse::success($tahunAkademik, 'Tahun akademik berhasil diperbarui');
        } catch (ValidationException $e) {
            return ApiResponse::validationError($e->errors(), 'Validasi gagal');
        } catch (\Exception $e) {
            return ApiResponse::error('Gagal memperbarui tahun akademik: '.$e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        $tahunAkademik = TahunAkademik::find($id);

        if (! $tahunAkademik) {
            return ApiResponse::error('Data tahun akademik tidak ditemukan', 404);
        }

        try {
            $tahunAkademik->delete();

            return ApiResponse::success(null, 'Tahun akademik berhasil dihapus');
        } catch (\Exception $e) {
            return ApiResponse::error('Gagal menghapus tahun akademik: '.$e->getMessage(), 500);
        }
    }
}
