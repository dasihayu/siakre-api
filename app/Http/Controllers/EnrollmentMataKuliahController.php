<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\EnrollmentMataKuliah;
use Illuminate\Http\Request;

class EnrollmentMataKuliahController extends Controller
{
    public function index(Request $request)
    {
        $query = EnrollmentMataKuliah::with(['tahunAkademik', 'mataKuliah']);

        if ($request->filled('mata_kuliah_id')) {
            $query->where('mata_kuliah_id', $request->query('mata_kuliah_id'));
        }

        if ($request->filled('tahun_akademik_id')) {
            $query->where('tahun_akademik_id', $request->query('tahun_akademik_id'));
        }

        $data = $query->get();

        return ApiResponse::success($data, 'Berhasil mengambil data Enrollment Mata Kuliah');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tahun_akademik_id' => 'required|exists:tahun_akademik,id',
            'mata_kuliah_id' => 'required|exists:mata_kuliah,id',
            'dosen_id' => 'nullable|integer',
            'evidence' => 'nullable|integer',
            'mata_kuliah_angkatan_kelas_id' => 'nullable|integer',
        ]);

        $data = EnrollmentMataKuliah::create($request->all());

        return ApiResponse::success($data, 'Berhasil menambahkan data', 201);
    }

    public function show(string $id)
    {
        $data = EnrollmentMataKuliah::with(['tahunAkademik', 'mataKuliah'])->find($id);

        return $data ? ApiResponse::success($data, 'Berhasil mengambil detail data') : ApiResponse::error('Data tidak ditemukan', 404);
    }

    public function update(Request $request, string $id)
    {
        $data = EnrollmentMataKuliah::find($id);
        if (! $data) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        $request->validate([
            'tahun_akademik_id' => 'required|exists:tahun_akademik,id',
            'mata_kuliah_id' => 'required|exists:mata_kuliah,id',
            'dosen_id' => 'nullable|integer',
            'evidence' => 'nullable|integer',
            'mata_kuliah_angkatan_kelas_id' => 'nullable|integer',
        ]);

        $data->update($request->all());

        return ApiResponse::success($data, 'Berhasil mengupdate data');
    }

    public function destroy(string $id)
    {
        $data = EnrollmentMataKuliah::find($id);
        if (! $data) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        $data->delete();

        return ApiResponse::success(null, 'Berhasil menghapus data');
    }
}
