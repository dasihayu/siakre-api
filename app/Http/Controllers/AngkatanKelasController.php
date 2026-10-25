<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\AngkatanKelas;
use App\Models\EnrollmentKelas;
use App\Models\MataKuliahAngkatanKelas;
use Illuminate\Http\Request;

class AngkatanKelasController extends Controller
{
    public function index(Request $request)
    {
        $query = AngkatanKelas::with(['kelas', 'angkatan', 'prodi', 'tahunAkademik'])
            ->withCount([
                'enrollmentKelas as jumlah_mahasiswa',
                'mataKuliahAngkatanKelas as jumlah_mata_kuliah',
            ]);

        if ($request->filled('tahun_akademik_id')) {
            $query->where('tahun_akademik_id', $request->query('tahun_akademik_id'));
        }

        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->query('kelas_id'));
        }

        if ($request->filled('angkatan_id')) {
            $query->where('angkatan_id', $request->query('angkatan_id'));
        }

        if ($request->filled('prodi_id')) {
            $query->where('prodi_id', $request->query('prodi_id'));
        }

        $data = $query->get();

        return ApiResponse::success($data, 'Berhasil mengambil data Angkatan Kelas');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'angkatan_id' => 'required|exists:angkatan,id',
            'prodi_id' => 'required|exists:prodi,id',
            'tahun_akademik_id' => 'required|exists:tahun_akademik,id',
        ]);

        $angkatanKelas = AngkatanKelas::create($request->all());

        return ApiResponse::success($angkatanKelas, 'Data Angkatan Kelas berhasil ditambahkan', 201);
    }

    public function show(string $id)
    {
        $angkatanKelas = AngkatanKelas::with(['kelas', 'angkatan', 'prodi', 'tahunAkademik'])->find($id);

        if (! $angkatanKelas) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        return ApiResponse::success($angkatanKelas, 'Berhasil mengambil detail data Angkatan Kelas');
    }

    public function update(Request $request, string $id)
    {
        $angkatanKelas = AngkatanKelas::find($id);

        if (! $angkatanKelas) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'angkatan_id' => 'required|exists:angkatan,id',
            'prodi_id' => 'required|exists:prodi,id',
            'tahun_akademik_id' => 'required|exists:tahun_akademik,id',
        ]);

        $angkatanKelas->update($request->all());

        return ApiResponse::success($angkatanKelas, 'Data Angkatan Kelas berhasil diupdate');
    }

    public function destroy(string $id)
    {
        $angkatanKelas = AngkatanKelas::find($id);

        if (! $angkatanKelas) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        $angkatanKelas->delete();

        return ApiResponse::success(null, 'Data berhasil dihapus');
    }

    public function mahasiswa(string $id)
    {
        $angkatanKelas = AngkatanKelas::find($id);

        if (! $angkatanKelas) {
            return ApiResponse::error('Data Angkatan Kelas tidak ditemukan', 404);
        }

        $enrollments = EnrollmentKelas::with(['mahasiswa', 'prodi'])
            ->where('angkatan_kelas_id', $id)
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
                'prodi' => $enrollment->prodi,
                'mahasiswa' => $mhs,
            ];
        });

        return ApiResponse::success($data, 'Berhasil mengambil data Mahasiswa per Angkatan Kelas');
    }

    public function mataKuliah(string $id)
    {
        $angkatanKelas = AngkatanKelas::find($id);

        if (! $angkatanKelas) {
            return ApiResponse::error('Data Angkatan Kelas tidak ditemukan', 404);
        }

        $mkAk = MataKuliahAngkatanKelas::with([
            'enrollmentMataKuliah.mataKuliah.kurikulum',
            'enrollmentMataKuliah.tahunAkademik',
        ])->where('angkatan_kelas_id', $id)->get();

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
                'mata_kuliah' => $matkul,
                'enrollment_mata_kuliah' => $emk,
            ];
        });

        return ApiResponse::success($data, 'Berhasil mengambil data Mata Kuliah per Angkatan Kelas');
    }
}
