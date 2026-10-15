<?php

namespace App\Http\Controllers;

use App\Models\AnggotaUpps;
use Illuminate\Http\Request;
use App\Helpers\ApiResponse;

class AnggotaUppsController extends Controller
{
    public function index()
    {
        // Mengambil data anggota beserta data UPPS yang menaunginya
        $data = AnggotaUpps::with('upps')->get();
        return ApiResponse::success($data, 'Berhasil mengambil data Anggota UPPS');
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_upps' => 'required|exists:upps,id',
            'nama'    => 'required|string',
            'jabatan' => 'required|string',
        ]);

        $anggota = AnggotaUpps::create($request->all());
        
        return ApiResponse::success($anggota, 'Data Anggota UPPS berhasil ditambahkan', 201);
    }

    public function show(string $id)
    {
        $anggota = AnggotaUpps::with('upps')->find($id);
        
        if (!$anggota) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }
        
        return ApiResponse::success($anggota, 'Berhasil mengambil detail data Anggota UPPS');
    }

    public function update(Request $request, string $id)
    {
        $anggota = AnggotaUpps::find($id);
        
        if (!$anggota) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        $request->validate([
            'id_upps' => 'required|exists:upps,id',
            'nama'    => 'required|string',
            'jabatan' => 'required|string',
        ]);

        $anggota->update($request->all());
        
        return ApiResponse::success($anggota, 'Data Anggota UPPS berhasil diupdate');
    }

    public function destroy(string $id)
    {
        $anggota = AnggotaUpps::find($id);
        
        if (!$anggota) {
            return ApiResponse::error('Data tidak ditemukan', 404);
        }

        $anggota->delete();
        return ApiResponse::success(null, 'Data berhasil dihapus');
    }
}