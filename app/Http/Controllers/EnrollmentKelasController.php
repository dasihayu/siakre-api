<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\EnrollmentKelas;
use Illuminate\Http\Request;

class EnrollmentKelasController extends Controller
{
    public function index(Request $request)
    {
        $query = EnrollmentKelas::with([
            'mahasiswa:id,nim,nama_mahasiswa,tahun_masuk,jenis_pendaftaran,tanggal_kelulusan,masa_studi_bulan',
            'angkatanKelas:id,kelas_id,angkatan_id,prodi_id,tahun_akademik_id',
            'prodi:id,kode_prodi,nama_prodi',
        ]);

        if ($request->filled('angkatan_kelas_id')) {
            $query->where('angkatan_kelas_id', $request->query('angkatan_kelas_id'));
        }

        if ($request->filled('id_mahasiswa')) {
            $query->where('id_mahasiswa', $request->query('id_mahasiswa'));
        }

        if ($request->filled('tahun_akademik_id')) {
            $taId = $request->query('tahun_akademik_id');
            $query->whereHas('angkatanKelas', fn ($q) => $q->where('tahun_akademik_id', $taId));
        }

        $data = $query->get();

        return ApiResponse::success($data, 'Berhasil mengambil data Enrollment Mahasiswa');
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_mahasiswa' => 'required|exists:data_mahasiswa,id',
            'angkatan_kelas_id' => 'required|exists:angkatan_kelas,id',
            'prodi_id' => 'required|exists:prodi,id',
        ]);

        // Opsional: Bisa ditambahkan validasi agar 1 mahasiswa tidak enroll 2x di kelas yang sama
        $exists = EnrollmentKelas::where('id_mahasiswa', $request->id_mahasiswa)
            ->where('angkatan_kelas_id', $request->angkatan_kelas_id)
            ->exists();
        if ($exists) {
            return ApiResponse::error('Mahasiswa sudah terdaftar di kelas ini', 422);
        }

        $enrollment = EnrollmentKelas::create($request->all());

        return ApiResponse::success($enrollment, 'Data Enrollment berhasil ditambahkan', 201);
    }

    public function show(string $id)
    {
        $enrollment = EnrollmentKelas::with(['mahasiswa', 'angkatanKelas', 'prodi'])->find($id);

        if (! $enrollment) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        return ApiResponse::success($enrollment, 'Berhasil mengambil detail data Enrollment');
    }

    public function update(Request $request, string $id)
    {
        $enrollment = EnrollmentKelas::find($id);

        if (! $enrollment) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        $request->validate([
            'id_mahasiswa' => 'required|exists:data_mahasiswa,id',
            'angkatan_kelas_id' => 'required|exists:angkatan_kelas,id',
            'prodi_id' => 'required|exists:prodi,id',
        ]);

        $enrollment->update($request->all());

        return ApiResponse::success($enrollment, 'Data Enrollment berhasil diupdate');
    }

    public function destroy(string $id)
    {
        $enrollment = EnrollmentKelas::find($id);

        if (! $enrollment) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        $enrollment->delete();

        return ApiResponse::success(null, 'Data berhasil dihapus');
    }
}
