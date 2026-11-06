<?php

namespace App\Http\Controllers;

use App\Enums\StatusKurikulum;
use App\Helpers\ApiResponse;
use App\Http\Requests\Kurikulum\StoreKurikulumRequest;
use App\Http\Requests\Kurikulum\UpdateKurikulumRequest;
use App\Models\Kurikulum;
use App\Models\MataKuliah;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class KurikulumController extends Controller
{
    // Tampil semua data
    public function index(Request $request)
    {
        $query = Kurikulum::with('prodi');

        if ($request->filled('id_prodi')) {
            $query->where('id_prodi', $request->query('id_prodi'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        $data = $query->get();

        return ApiResponse::success($data, 'Berhasil mengambil data Kurikulum');
    }

    // Tambah data (Sesuai Flowchart: Input Nama Kurikulum & Tahun -> Simpan)
    public function store(StoreKurikulumRequest $request)
    {
        $validated = $request->validated();
        if (! isset($validated['status'])) {
            $validated['status'] = StatusKurikulum::AKTIF->value;
        }

        $kurikulum = Kurikulum::create($validated);

        return ApiResponse::success($kurikulum, 'Data Kurikulum berhasil ditambahkan', 201);
    }

    // Tampil satu data spesifik
    public function show(string $id)
    {
        $kurikulum = Kurikulum::with('prodi')->find($id);

        if (! $kurikulum) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        return ApiResponse::success($kurikulum, 'Berhasil mengambil detail data Kurikulum');
    }

    // Update data (Sesuai Flowchart: Ubah Data / SK Kurikulum -> Update)
    public function update(UpdateKurikulumRequest $request, string $id)
    {
        $kurikulum = Kurikulum::find($id);

        if (! $kurikulum) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        $kurikulum->update($request->validated());

        return ApiResponse::success($kurikulum, 'Data Kurikulum berhasil diupdate');
    }

    // Hapus data (Sesuai Flowchart: Cek Relasi ke Mata Kuliah -> Hapus)
    public function destroy(string $id)
    {
        $kurikulum = Kurikulum::find($id);

        if (! $kurikulum) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        try {
            // Langsung eksekusi hapus. Biarkan database yang mengecek relasi ke CPL/Matkul
            $kurikulum->delete();

            return ApiResponse::success(null, 'Data berhasil dihapus');

        } catch (QueryException $e) {
            // Jika database menolak karena data id_kurikulum masih dipakai di tabel Matkul / CPL
            if ($e->getCode() == '23000') {
                return ApiResponse::error('Kurikulum Memiliki Matkul/CPL', 400);
            }

            return ApiResponse::error('Gagal menghapus data', 500);
        }
    }

    // Tampil daftar mata kuliah per kurikulum
    public function mataKuliah(string $id)
    {
        $kurikulum = Kurikulum::find($id);

        if (! $kurikulum) {
            return ApiResponse::error('Data kurikulum tidak ditemukan', 404);
        }

        $data = MataKuliah::with(['kurikulum', 'cpl'])
            ->where('id_kurikulum', $id)
            ->get();

        return ApiResponse::success($data, 'Berhasil mengambil data Mata Kuliah per Kurikulum');
    }
}
