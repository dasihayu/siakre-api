<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\AnggotaUpps;
use Illuminate\Http\Request;

class AnggotaUppsController extends Controller
{
    public function index()
    {
        // Mengambil data anggota beserta data UPPS yang menaunginya
        $data = AnggotaUpps::with(['upps', 'parent', 'children'])
            ->orderBy('urutan_tampil')
            ->get();

        return ApiResponse::success($data, 'Berhasil mengambil data Anggota UPPS');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_upps' => 'required|exists:upps,id',
            'nama' => 'required|string',
            'jabatan' => 'required|string',
            'parent_id' => 'nullable|exists:anggota_upps,id',
            'urutan_tampil' => 'sometimes|integer|min:0',
            'periode_mulai' => 'nullable|date',
            'periode_selesai' => 'nullable|date|after_or_equal:periode_mulai',
        ]);

        $anggota = AnggotaUpps::create($validated);

        return ApiResponse::success($anggota, 'Data Anggota UPPS berhasil ditambahkan', 201);
    }

    public function show(string $id)
    {
        $anggota = AnggotaUpps::with('upps')->find($id);

        if (! $anggota) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        return ApiResponse::success($anggota, 'Berhasil mengambil detail data Anggota UPPS');
    }

    public function update(Request $request, string $id)
    {
        $anggota = AnggotaUpps::find($id);

        if (! $anggota) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        $validated = $request->validate([
            'id_upps' => 'required|exists:upps,id',
            'nama' => 'required|string',
            'jabatan' => 'required|string',
            'parent_id' => 'nullable|exists:anggota_upps,id',
            'urutan_tampil' => 'sometimes|integer|min:0',
            'periode_mulai' => 'nullable|date',
            'periode_selesai' => 'nullable|date|after_or_equal:periode_mulai',
        ]);

        $anggota->update($validated);

        return ApiResponse::success($anggota, 'Data Anggota UPPS berhasil diupdate');
    }

    public function destroy(string $id)
    {
        $anggota = AnggotaUpps::find($id);

        if (! $anggota) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        $anggota->delete();

        return ApiResponse::success(null, 'Data berhasil dihapus');
    }
}
