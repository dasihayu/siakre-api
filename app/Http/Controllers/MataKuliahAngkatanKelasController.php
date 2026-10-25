<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\MataKuliahAngkatanKelas;
use Illuminate\Http\Request;

class MataKuliahAngkatanKelasController extends Controller
{
    public function index(Request $request)
    {
        $query = MataKuliahAngkatanKelas::with(['enrollmentMataKuliah', 'angkatanKelas']);

        if ($request->filled('angkatan_kelas_id')) {
            $query->where('angkatan_kelas_id', $request->query('angkatan_kelas_id'));
        }

        if ($request->filled('enrollment_mata_kuliah_id')) {
            $query->where('enrollment_mata_kuliah_id', $request->query('enrollment_mata_kuliah_id'));
        }

        if ($request->filled('mata_kuliah_id')) {
            $mkId = $request->query('mata_kuliah_id');
            $targetTaId = $request->filled('tahun_akademik_id')
                ? (int) $request->query('tahun_akademik_id')
                : EnrollmentMataKuliahController::resolveActiveTahunAkademikIdForCourse($mkId);

            $query->whereHas('enrollmentMataKuliah', function ($q) use ($mkId, $targetTaId) {
                $q->where('mata_kuliah_id', $mkId);
                if ($targetTaId) {
                    $q->where('tahun_akademik_id', $targetTaId);
                }
            });
        } elseif ($request->filled('tahun_akademik_id')) {
            $taId = (int) $request->query('tahun_akademik_id');
            $query->whereHas('angkatanKelas', fn ($q) => $q->where('tahun_akademik_id', $taId));
        }

        $data = $query->get();

        return ApiResponse::success($data, 'Berhasil mengambil data Mata Kuliah Angkatan Kelas');
    }

    public function store(Request $request)
    {
        $request->validate([
            'enrollment_mata_kuliah_id' => 'required|exists:enrollment_mata_kuliah,id',
            'angkatan_kelas_id' => 'required|exists:angkatan_kelas,id',
        ]);

        $data = MataKuliahAngkatanKelas::create($request->all());

        return ApiResponse::success($data, 'Berhasil menambahkan data', 201);
    }

    public function show(string $id)
    {
        $data = MataKuliahAngkatanKelas::with(['enrollmentMataKuliah', 'angkatanKelas'])->find($id);

        return $data ? ApiResponse::success($data, 'Berhasil mengambil detail data') : ApiResponse::error('Data tidak ditemukan', 404);
    }

    public function update(Request $request, string $id)
    {
        $data = MataKuliahAngkatanKelas::find($id);
        if (! $data) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        $request->validate([
            'enrollment_mata_kuliah_id' => 'required|exists:enrollment_mata_kuliah,id',
            'angkatan_kelas_id' => 'required|exists:angkatan_kelas,id',
        ]);

        $data->update($request->all());

        return ApiResponse::success($data, 'Berhasil mengupdate data');
    }

    public function destroy(string $id)
    {
        $data = MataKuliahAngkatanKelas::find($id);
        if (! $data) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        $data->delete();

        return ApiResponse::success(null, 'Berhasil menghapus data');
    }
}
