<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\KegiatanMahasiswa;
use Illuminate\Http\Request;

class KegiatanMahasiswaController extends Controller
{
    public function index()
    {
        $data = KegiatanMahasiswa::with(['mahasiswa', 'tahunAkademik', 'evidences'])->get();

        return ApiResponse::success($data, 'Berhasil mengambil data kegiatan mahasiswa');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'mahasiswa_id' => 'required|exists:data_mahasiswa,id',
            'tahun_akademik_id' => 'required|exists:tahun_akademik,id',
            'nama_kegiatan' => 'required|string',
            'jenis_kegiatan' => 'required|string',
            'peran' => 'required|string',
            'tingkat' => 'required|in:Internasional,Nasional,Provinsi,Lokal',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'penyelenggara' => 'required|string',
            'status_validasi' => 'required|in:Pending,Valid,Ditolak',
        ]);

        $kegiatan = KegiatanMahasiswa::create($validated);
        $kegiatan->load('evidences');

        return ApiResponse::success($kegiatan, 'Data kegiatan mahasiswa berhasil ditambahkan', 201);
    }

    public function show(string $id)
    {
        $kegiatan = KegiatanMahasiswa::with(['mahasiswa', 'tahunAkademik', 'evidences'])->find($id);

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

        $validated = $request->validate([
            'mahasiswa_id' => 'required|exists:data_mahasiswa,id',
            'tahun_akademik_id' => 'required|exists:tahun_akademik,id',
            'nama_kegiatan' => 'required|string',
            'jenis_kegiatan' => 'required|string',
            'peran' => 'required|string',
            'tingkat' => 'required|in:Internasional,Nasional,Provinsi,Lokal',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'penyelenggara' => 'required|string',
            'status_validasi' => 'required|in:Pending,Valid,Ditolak',
        ]);

        $kegiatan->update($validated);
        $kegiatan->load('evidences');

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
