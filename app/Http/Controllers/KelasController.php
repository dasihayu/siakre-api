<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\AngkatanKelas;
use App\Models\EnrollmentKelas;
use App\Models\Kelas;
use App\Models\MataKuliahAngkatanKelas;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class KelasController extends Controller
{
    public function index(Request $request)
    {
        try {
            $kelas = $request->has('per_page') ? Kelas::paginate($request->get('per_page', 10)) : Kelas::all();

            return ApiResponse::success($kelas, 'Berhasil mendapatkan data kelas');
        } catch (\Exception $e) {
            return ApiResponse::error('Gagal memuat data kelas: '.$e->getMessage(), 500);
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
            return ApiResponse::error('Gagal menambahkan kelas: '.$e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        $kelas = Kelas::find($id);

        if (! $kelas) {
            return ApiResponse::error('Data kelas tidak ditemukan', 404);
        }

        return ApiResponse::success($kelas, 'Detail kelas berhasil ditemukan');
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

    public function mahasiswa(Request $request, string $id)
    {
        $kelas = Kelas::find($id);

        if (! $kelas) {
            return ApiResponse::error('Data kelas tidak ditemukan', 404);
        }

        $query = AngkatanKelas::where('kelas_id', $id);
        if ($request->filled('angkatan_id')) {
            $query->where('angkatan_id', $request->angkatan_id);
        }
        if ($request->filled('tahun_akademik_id')) {
            $query->where('tahun_akademik_id', $request->tahun_akademik_id);
        }
        $angkatanKelasIds = $query->pluck('id');

        $enrollments = EnrollmentKelas::with(['mahasiswa', 'angkatanKelas', 'prodi'])
            ->whereIn('angkatan_kelas_id', $angkatanKelasIds)
            ->get();

        $data = $enrollments->map(function ($enrollment) {
            $mhs = $enrollment->mahasiswa;

            return [
                'id' => $mhs?->id,
                'nim' => $mhs?->nim,
                'nama_mahasiswa' => $mhs?->nama_mahasiswa,
                'tahun_masuk' => $mhs?->tahun_masuk,
                'jenis_pendaftaran' => $mhs?->jenis_pendaftaran,
                'tanggal_kelulusan' => $mhs?->tanggal_kelulusan,
                'masa_studi_bulan' => $mhs?->masa_studi_bulan,
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

        $query = AngkatanKelas::where('kelas_id', $id);
        if ($request->filled('angkatan_id')) {
            $query->where('angkatan_id', $request->angkatan_id);
        }
        if ($request->filled('tahun_akademik_id')) {
            $query->where('tahun_akademik_id', $request->tahun_akademik_id);
        }
        $angkatanKelasIds = $query->pluck('id');

        $mkAk = MataKuliahAngkatanKelas::with([
            'enrollmentMataKuliah.mataKuliah.kurikulum',
            'enrollmentMataKuliah.tahunAkademik',
            'angkatanKelas',
        ])->whereIn('angkatan_kelas_id', $angkatanKelasIds)->get();

        $data = $mkAk->map(function ($item) {
            $matkul = $item->enrollmentMataKuliah?->mataKuliah;
            $emk = $item->enrollmentMataKuliah;

            return [
                'id' => $matkul?->id,
                'mata_kuliah_angkatan_kelas_id' => $item->id,
                'enrollment_mata_kuliah_id' => $item->enrollment_mata_kuliah_id,
                'angkatan_kelas_id' => $item->angkatan_kelas_id,
                'kode_mata_kuliah' => $matkul?->kode_mata_kuliah,
                'nama_mata_kuliah' => $matkul?->nama_mata_kuliah,
                'sks' => $matkul?->sks,
                'id_kurikulum' => $matkul?->id_kurikulum,
                'kurikulum' => $matkul?->kurikulum,
                'tahun_akademik_id' => $emk?->tahun_akademik_id,
                'tahun_akademik' => $emk?->tahunAkademik,
                'dosen_id' => $emk?->dosen_id,
                'evidence' => $emk?->evidence,
                'angkatan_kelas' => $item->angkatanKelas,
                'mata_kuliah' => $matkul,
                'enrollment_mata_kuliah' => $emk,
            ];
        });

        return ApiResponse::success($data, 'Berhasil mendapatkan data mata kuliah per kelas');
    }
}
