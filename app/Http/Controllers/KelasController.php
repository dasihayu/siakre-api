<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\AngkatanKelas;
use App\Models\EnrollmentKelas;
use App\Models\Kelas;
use App\Models\MataKuliahAngkatanKelas;
use App\Models\Prodi;
use App\Models\TahunAkademik;
use App\Services\AkademikCalculatorService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class KelasController extends Controller
{
    public function index(Request $request)
    {
        try {
            $taId = $request->filled('tahun_akademik_id')
                ? (int) $request->query('tahun_akademik_id')
                : (TahunAkademik::where('is_active', true)->value('id') ?? TahunAkademik::latest('id')->value('id'));

            $query = Kelas::query();

            if ($request->filled('tahun_akademik_id')) {
                $kelasIdsForTa = AngkatanKelas::where('tahun_akademik_id', $taId)->pluck('kelas_id');
                if ($kelasIdsForTa->isNotEmpty()) {
                    $query->whereIn('id', $kelasIdsForTa);
                }
            }

            $akMap = collect();
            if ($taId) {
                $akMap = AngkatanKelas::with(['angkatan', 'prodi', 'tahunAkademik'])
                    ->withCount([
                        'enrollmentKelas as jumlah_mahasiswa',
                        'mataKuliahAngkatanKelas as jumlah_mata_kuliah',
                    ])
                    ->where('tahun_akademik_id', $taId)
                    ->get()
                    ->keyBy('kelas_id');
            }

            $defaultProdi = Prodi::first();
            $defaultTa = $taId ? TahunAkademik::find($taId) : null;

            $transform = fn (Kelas $k) => $this->formatKelasWithContext($k, $akMap->get($k->id), $defaultProdi, $defaultTa);

            if ($request->has('per_page')) {
                /** @var LengthAwarePaginator $paginator */
                $paginator = $query->paginate($request->get('per_page', 10));
                $paginator->getCollection()->transform($transform);

                return ApiResponse::success($paginator, 'Berhasil mendapatkan data kelas');
            }

            $kelas = $query->get()->map($transform);

            return ApiResponse::success($kelas, 'Berhasil mendapatkan data kelas');
        } catch (\Exception $e) {
            return ApiResponse::error('Gagal memuat data kelas: '.$e->getMessage(), 500);
        }
    }

    protected function formatKelasWithContext(
        Kelas $kelas,
        ?AngkatanKelas $ak = null,
        ?Prodi $defaultProdi = null,
        ?TahunAkademik $defaultTa = null
    ): array {
        $prodi = $ak?->prodi ?? $defaultProdi;
        $ta = $ak?->tahunAkademik ?? $defaultTa;

        return [
            'id' => $kelas->id,
            'kelas' => $kelas->kelas,
            'nama' => $kelas->kelas,
            'nama_kelas' => $kelas->kelas,
            'angkatan_kelas_id' => $ak?->id,
            'angkatan_id' => $ak?->angkatan_id,
            'angkatan' => $ak?->angkatan,
            'prodi_id' => $prodi?->id,
            'prodi' => $prodi ? [
                'id' => $prodi->id,
                'kode_prodi' => $prodi->kode_prodi,
                'nama_prodi' => $prodi->nama_prodi,
                'nama' => $prodi->nama_prodi,
            ] : null,
            'tahun_akademik_id' => $ta?->id,
            'tahun_akademik' => $ta,
            'jumlah_mahasiswa' => (int) ($ak?->jumlah_mahasiswa ?? 0),
            'jumlah_mata_kuliah' => (int) ($ak?->jumlah_mata_kuliah ?? 0),
            'created_at' => $kelas->created_at,
            'updated_at' => $kelas->updated_at,
        ];
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
            return ApiResponse::error('Gagal menambahkan kelas: '.$e->getMessage(), 500);
        }
    }

    public function show(Request $request, $id)
    {
        $kelas = Kelas::find($id);

        if (! $kelas) {
            return ApiResponse::error('Data kelas tidak ditemukan', 404);
        }

        $taId = $request->filled('tahun_akademik_id')
            ? (int) $request->query('tahun_akademik_id')
            : (TahunAkademik::where('is_active', true)->value('id') ?? TahunAkademik::latest('id')->value('id'));

        $ak = $taId
            ? AngkatanKelas::with(['angkatan', 'prodi', 'tahunAkademik'])
                ->withCount([
                    'enrollmentKelas as jumlah_mahasiswa',
                    'mataKuliahAngkatanKelas as jumlah_mata_kuliah',
                ])
                ->where('kelas_id', $kelas->id)
                ->where('tahun_akademik_id', $taId)
                ->first()
            : null;

        return ApiResponse::success(
            $this->formatKelasWithContext($kelas, $ak, Prodi::first(), $taId ? TahunAkademik::find($taId) : null),
            'Detail kelas berhasil ditemukan'
        );
    }

    public function update(Request $request, $id)
    {
        $kelas = Kelas::find($id);

        if (! $kelas) {
            return ApiResponse::error('Data kelas tidak ditemukan', 404);
        }

        try {
            $validated = $request->validate([
                'kelas' => 'required|string|max:50|unique:kelas,kelas,'.$id,
            ]);

            $kelas->update($validated);

            return ApiResponse::success($kelas, 'Kelas berhasil diperbarui');
        } catch (ValidationException $e) {
            return ApiResponse::validationError($e->errors(), 'Validasi gagal');
        } catch (\Exception $e) {
            return ApiResponse::error('Gagal memperbarui kelas: '.$e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        $kelas = Kelas::find($id);

        if (! $kelas) {
            return ApiResponse::error('Data kelas tidak ditemukan', 404);
        }

        try {
            $kelas->delete();

            return ApiResponse::success(null, 'Kelas berhasil dihapus');
        } catch (\Exception $e) {
            return ApiResponse::error('Gagal menghapus kelas: '.$e->getMessage(), 500);
        }
    }

    protected function resolveAngkatanKelasIdsForClass(Request $request, int|string $kelasId)
    {
        $query = AngkatanKelas::where('kelas_id', $kelasId);

        if ($request->filled('angkatan_id')) {
            $query->where('angkatan_id', $request->angkatan_id);
        }

        if ($request->filled('tahun_akademik_id')) {
            $query->where('tahun_akademik_id', $request->tahun_akademik_id);

            return $query->pluck('id');
        }

        if (! $request->filled('angkatan_id')) {
            $activeTaId = TahunAkademik::where('is_active', true)->value('id');
            if ($activeTaId) {
                $activeIds = (clone $query)->where('tahun_akademik_id', $activeTaId)->pluck('id');
                if ($activeIds->isNotEmpty()) {
                    return $activeIds;
                }
            }
        }

        return $query->pluck('id');
    }

    public function mahasiswa(Request $request, string $id)
    {
        $kelas = Kelas::find($id);

        if (! $kelas) {
            return ApiResponse::error('Data kelas tidak ditemukan', 404);
        }

        $angkatanKelasIds = $this->resolveAngkatanKelasIdsForClass($request, $id);

        $enrollments = EnrollmentKelas::with([
            'statusMahasiswa',
            'mahasiswa.prestasiMahasiswa',
            'mahasiswa.nilaiMahasiswa.mataKuliahAngkatanKelas.enrollmentMataKuliah.mataKuliah',
            'angkatanKelas.angkatan',
            'angkatanKelas.tahunAkademik',
            'prodi',
        ])
            ->whereIn('angkatan_kelas_id', $angkatanKelasIds)
            ->get();

        $data = $enrollments->map(function ($enrollment) {
            $mhs = $enrollment->mahasiswa;
            $latestStatus = $enrollment->statusMahasiswa?->status ?? ($mhs?->tanggal_kelulusan ? 'Lulus' : 'Aktif');
            $ipkResult = $mhs ? AkademikCalculatorService::hitungIPK($mhs->nilaiMahasiswa ?? collect()) : ['ipk' => 0.0];

            return [
                'id' => $mhs?->id,
                'nim' => $mhs?->nim,
                'nim_display' => $mhs?->nim_display,
                'nama' => $mhs?->nama_mahasiswa,
                'nama_mahasiswa' => $mhs?->nama_mahasiswa,
                'tahun_masuk' => $mhs?->tahun_masuk,
                'jenis_pendaftaran' => $mhs?->jenis_pendaftaran,
                'tanggal_kelulusan' => $mhs?->tanggal_kelulusan,
                'masa_studi_bulan' => $mhs?->masa_studi_bulan,
                'status' => $latestStatus,
                'ipk' => $ipkResult['ipk'],
                'jumlah_prestasi' => $mhs?->prestasiMahasiswa?->count() ?? 0,
                'enrollment_id' => $enrollment->id,
                'enrollment_mahasiswa_angkatan_kelas_id' => $enrollment->id,
                'angkatan_kelas_id' => $enrollment->angkatan_kelas_id,
                'prodi_id' => $enrollment->prodi_id,
                'angkatan_kelas' => $enrollment->angkatanKelas,
                'prodi' => $enrollment->prodi,
                'mahasiswa' => $mhs,
            ];
        });

        return ApiResponse::success($data, 'Berhasil mendapatkan data mahasiswa per kelas');
    }

    public function mataKuliah(Request $request, string $id)
    {
        $kelas = Kelas::find($id);

        if (! $kelas) {
            return ApiResponse::error('Data kelas tidak ditemukan', 404);
        }

        $angkatanKelasIds = $this->resolveAngkatanKelasIdsForClass($request, $id);

        $mkAk = MataKuliahAngkatanKelas::with([
            'enrollmentMataKuliah.mataKuliah.kurikulum',
            'enrollmentMataKuliah.tahunAkademik',
            'enrollmentMataKuliah.dosen',
            'angkatanKelas',
        ])->whereIn('angkatan_kelas_id', $angkatanKelasIds)->get();

        $data = $mkAk->map(function ($item) {
            $matkul = $item->enrollmentMataKuliah?->mataKuliah;
            $emk = $item->enrollmentMataKuliah;
            $dosenName = $emk?->dosen?->name;

            return [
                'id' => $matkul?->id,
                'mata_kuliah_angkatan_kelas_id' => $item->id,
                'enrollment_mata_kuliah_id' => $item->enrollment_mata_kuliah_id,
                'angkatan_kelas_id' => $item->angkatan_kelas_id,
                'kode' => $matkul?->kode_mata_kuliah,
                'kode_mk' => $matkul?->kode_mata_kuliah,
                'kode_mata_kuliah' => $matkul?->kode_mata_kuliah,
                'nama' => $matkul?->nama_mata_kuliah,
                'nama_mk' => $matkul?->nama_mata_kuliah,
                'nama_mata_kuliah' => $matkul?->nama_mata_kuliah,
                'sks' => $matkul?->sks,
                'id_kurikulum' => $matkul?->id_kurikulum,
                'kurikulum' => $matkul?->kurikulum,
                'tahun_akademik_id' => $emk?->tahun_akademik_id,
                'tahun_akademik' => $emk?->tahunAkademik,
                'dosen_id' => $emk?->dosen_id,
                'dosen' => $dosenName,
                'dosen_pengampu' => $dosenName,
                'evidence' => $emk?->evidence,
                'angkatan_kelas' => $item->angkatanKelas,
                'mata_kuliah' => $matkul,
                'enrollment_mata_kuliah' => $emk,
            ];
        });

        return ApiResponse::success($data, 'Berhasil mendapatkan data mata kuliah per kelas');
    }
}
