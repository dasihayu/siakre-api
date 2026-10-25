<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\AngkatanKelas;
use App\Models\Prodi;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TahunAkademikController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = TahunAkademik::query()
                ->withCount('angkatanKelas as jumlah_kelas')
                ->with(['angkatanKelas.prodi']);

            if ($request->has('semester')) {
                $query->where('semester', $request->query('semester'));
            }

            if ($request->has('tahun')) {
                $query->where('tahun', $request->query('tahun'));
            }

            if ($request->has('is_active')) {
                $query->where('is_active', filter_var($request->query('is_active'), FILTER_VALIDATE_BOOLEAN));
            }

            $defaultProdi = Prodi::first();

            if ($request->has('per_page')) {
                /** @var LengthAwarePaginator $paginator */
                $paginator = $query->paginate($request->get('per_page', 10));
                $paginator->getCollection()->transform(fn (TahunAkademik $ta) => $this->formatTahunAkademik($ta, $defaultProdi));

                return ApiResponse::success($paginator, 'Berhasil mendapatkan data tahun akademik');
            }

            $tahunAkademik = $query->get()->map(fn (TahunAkademik $ta) => $this->formatTahunAkademik($ta, $defaultProdi));

            return ApiResponse::success($tahunAkademik, 'Berhasil mendapatkan data tahun akademik');
        } catch (\Exception $e) {
            return ApiResponse::error('Gagal memuat data tahun akademik: '.$e->getMessage(), 500);
        }
    }

    protected function formatTahunAkademik(TahunAkademik $ta, ?Prodi $defaultProdi = null): array
    {
        $prodiModel = $ta->relationLoaded('angkatanKelas')
            ? $ta->angkatanKelas->first()?->prodi
            : null;
        $prodiModel = $prodiModel ?? $defaultProdi;

        return [
            'id' => $ta->id,
            'tahun' => $ta->tahun,
            'semester' => $ta->semester,
            'nama_tahun_akademik' => $ta->nama_tahun_akademik,
            'is_active' => (bool) $ta->is_active,
            'jumlah_kelas' => (int) ($ta->jumlah_kelas ?? $ta->angkatanKelas()->count()),
            'prodi' => $prodiModel ? [
                'id' => $prodiModel->id,
                'kode_prodi' => $prodiModel->kode_prodi,
                'nama_prodi' => $prodiModel->nama_prodi,
                'nama' => $prodiModel->nama_prodi,
            ] : null,
            'created_at' => $ta->created_at,
            'updated_at' => $ta->updated_at,
        ];
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
        $tahunAkademik = TahunAkademik::withCount('angkatanKelas as jumlah_kelas')
            ->with(['angkatanKelas.prodi'])
            ->find($id);

        if (! $tahunAkademik) {
            return ApiResponse::error('Data tahun akademik tidak ditemukan', 404);
        }

        return ApiResponse::success(
            $this->formatTahunAkademik($tahunAkademik, Prodi::first()),
            'Detail tahun akademik berhasil ditemukan'
        );
    }

    public function kelas(Request $request, $id)
    {
        $tahunAkademik = TahunAkademik::find($id);

        if (! $tahunAkademik) {
            return ApiResponse::error('Data tahun akademik tidak ditemukan', 404);
        }

        $query = AngkatanKelas::with(['kelas', 'angkatan', 'prodi', 'tahunAkademik'])
            ->withCount([
                'enrollmentKelas as jumlah_mahasiswa',
                'mataKuliahAngkatanKelas as jumlah_mata_kuliah',
            ])
            ->where('tahun_akademik_id', $id)
            ->orderBy('kelas_id');

        $transform = function (AngkatanKelas $ak) {
            return [
                'id' => $ak->kelas_id,
                'angkatan_kelas_id' => $ak->id,
                'kelas' => $ak->kelas?->kelas,
                'nama' => $ak->kelas?->kelas,
                'nama_kelas' => $ak->kelas?->kelas,
                'angkatan_id' => $ak->angkatan_id,
                'angkatan' => $ak->angkatan,
                'prodi_id' => $ak->prodi_id,
                'prodi' => $ak->prodi ? [
                    'id' => $ak->prodi->id,
                    'kode_prodi' => $ak->prodi->kode_prodi,
                    'nama_prodi' => $ak->prodi->nama_prodi,
                    'nama' => $ak->prodi->nama_prodi,
                ] : null,
                'tahun_akademik_id' => $ak->tahun_akademik_id,
                'tahun_akademik' => $ak->tahunAkademik,
                'jumlah_mahasiswa' => (int) ($ak->jumlah_mahasiswa ?? 0),
                'jumlah_mata_kuliah' => (int) ($ak->jumlah_mata_kuliah ?? 0),
            ];
        };

        if ($request->has('per_page')) {
            /** @var LengthAwarePaginator $paginator */
            $paginator = $query->paginate($request->get('per_page', 10));
            $paginator->getCollection()->transform($transform);

            return ApiResponse::success($paginator, 'Berhasil mendapatkan daftar kelas pada tahun akademik');
        }

        return ApiResponse::success(
            $query->get()->map($transform),
            'Berhasil mendapatkan daftar kelas pada tahun akademik'
        );
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
