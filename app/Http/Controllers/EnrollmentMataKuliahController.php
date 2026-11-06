<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\EnrollmentMataKuliah\StoreEnrollmentMataKuliahRequest;
use App\Http\Requests\EnrollmentMataKuliah\UpdateEnrollmentMataKuliahRequest;
use App\Models\EnrollmentMataKuliah;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;

class EnrollmentMataKuliahController extends Controller
{
    public function index(Request $request)
    {
        $query = EnrollmentMataKuliah::with(['tahunAkademik', 'mataKuliah', 'dosen']);

        if ($request->filled('mata_kuliah_id')) {
            $query->where('mata_kuliah_id', $request->query('mata_kuliah_id'));
        }

        if ($request->filled('tahun_akademik_id')) {
            $query->where('tahun_akademik_id', $request->query('tahun_akademik_id'));
        }

        if ($request->filled('dosen_id')) {
            $query->where('dosen_id', $request->query('dosen_id'));
        }

        $data = $query->get();

        return ApiResponse::success($data, 'Berhasil mengambil data Enrollment Mata Kuliah');
    }

    public static function resolveActiveTahunAkademikIdForCourse(int|string $mataKuliahId): ?int
    {
        $activeTa = TahunAkademik::where('is_active', true)->first();
        if (! $activeTa) {
            return null;
        }

        // 1. Jika mata kuliah ditawarkan langsung di Tahun Akademik yang aktif
        $hasDirect = EnrollmentMataKuliah::where('mata_kuliah_id', $mataKuliahId)
            ->where('tahun_akademik_id', $activeTa->id)
            ->exists();
        if ($hasDirect) {
            return (int) $activeTa->id;
        }

        // 2. Jika mata kuliah berbeda semester (misal MK Genap saat TA Aktif Ganjil), ambil pasangan tahun yang sama (misal 2026/2027)
        $sameYearTaIds = TahunAkademik::where('tahun', $activeTa->tahun)->pluck('id');
        $matchedTaId = EnrollmentMataKuliah::where('mata_kuliah_id', $mataKuliahId)
            ->whereIn('tahun_akademik_id', $sameYearTaIds)
            ->value('tahun_akademik_id');

        if ($matchedTaId) {
            return (int) $matchedTaId;
        }

        // 3. Fallback ke tahun akademik terbaru tempat mata kuliah ini ditawarkan
        $latestTaId = EnrollmentMataKuliah::where('mata_kuliah_id', $mataKuliahId)
            ->orderByDesc('tahun_akademik_id')
            ->value('tahun_akademik_id');

        return $latestTaId ? (int) $latestTaId : null;
    }

    public function store(StoreEnrollmentMataKuliahRequest $request)
    {
        $data = EnrollmentMataKuliah::create($request->validated());

        return ApiResponse::success($data, 'Berhasil menambahkan data', 201);
    }

    public function show(string $id)
    {
        $data = EnrollmentMataKuliah::with(['tahunAkademik', 'mataKuliah', 'dosen'])->find($id);

        return $data ? ApiResponse::success($data, 'Berhasil mengambil detail data') : ApiResponse::error('Data tidak ditemukan', 404);
    }

    public function update(UpdateEnrollmentMataKuliahRequest $request, string $id)
    {
        $data = EnrollmentMataKuliah::find($id);
        if (! $data) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        $data->update($request->validated());

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
