<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\KegiatanMahasiswa;
use Illuminate\Http\Request;

class KegiatanMahasiswaController extends Controller
{
    public function index()
    {
        $data = KegiatanMahasiswa::with(['mahasiswa', 'tahunAkademik'])->get();

        return ApiResponse::success($data, 'Berhasil mengambil data kegiatan mahasiswa');
    }

    public function store(Request $request)
    {
        $request->validate([
            'mahasiswa_id' => 'required|exists:data_mahasiswa,id',
            'tahun_akademik_id' => 'required|exists:tahun_akademik,id',
            'nama_kegiatan' => 'required|string',
            'jenis_kegiatan' => 'required|string',
            'peran' => 'required|string',
            'tingkat' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'penyelenggara' => 'required|string',
            'status_validasi' => 'required|string',
        ]);

        $kegiatan = KegiatanMahasiswa::create($request->all());

        return ApiResponse::success($kegiatan, 'Data kegiatan mahasiswa berhasil ditambahkan', 201);
    }

    public function show(string $id)
    {
        $kegiatan = KegiatanMahasiswa::with(['mahasiswa', 'tahunAkademik'])->find($id);

        if (! $kegiatan) {
            return ApiResponse::error('Data kegiatan tidak ditemukan', 404);
        }

        return ApiResponse::success($kegiatan, 'Berhasil mengambil detail data kegiatan');
    }

    public function update(Request $request, string $id)
    {
        $kegiatan = KegiatanMahasiswa::find($id);

        if (! $kegiatan) {
            return ApiResponse::error('Data kegiatan tidak ditemukan', 404);
        }

        $request->validate([
            'mahasiswa_id' => 'required|exists:data_mahasiswa,id',
            'tahun_akademik_id' => 'required|exists:tahun_akademik,id',
            'nama_kegiatan' => 'required|string',
            'jenis_kegiatan' => 'required|string',
            'peran' => 'required|string',
            'tingkat' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'penyelenggara' => 'required|string',
            'status_validasi' => 'required|string',
        ]);

        $kegiatan->update($request->all());

        return ApiResponse::success($kegiatan, 'Data kegiatan mahasiswa berhasil diupdate');
    }

    public function destroy(string $id)
    {
        $kegiatan = KegiatanMahasiswa::find($id);

        if (! $kegiatan) {
            return ApiResponse::error('Data kegiatan tidak ditemukan', 404);
        }

        $kegiatan->delete();

        return ApiResponse::success(null, 'Data kegiatan berhasil dihapus');
    }
}
