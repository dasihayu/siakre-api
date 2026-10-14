<?php

namespace App\Http\Controllers;

use App\Models\DataKelulusan;
use Illuminate\Http\Request;
use App\Helpers\ApiResponse;

class DataKelulusanController extends Controller
{
    public function index()
    {
        $data = DataKelulusan::with('mahasiswa')->get();
        return ApiResponse::success($data, 'Berhasil mengambil data kelulusan');
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_mahasiswa'      => 'required|exists:data_mahasiswa,id',
            'tanggal_kelulusan' => 'required|date',
            'ipk'               => 'required|numeric|min:0|max:4',
            'masa_studi_bulan'  => 'required|integer|min:1',
        ]);

        $kelulusan = DataKelulusan::create($request->all());
        
        return ApiResponse::success($kelulusan, 'Data kelulusan berhasil ditambahkan', 201);
    }

    public function show(string $id)
    {
        $kelulusan = DataKelulusan::with('mahasiswa')->find($id);
        
        if (!$kelulusan) {
            return ApiResponse::error('Data kelulusan tidak ditemukan', 404);
        }
        
        return ApiResponse::success($kelulusan, 'Berhasil mengambil detail data kelulusan');
    }

    public function update(Request $request, string $id)
    {
        $kelulusan = DataKelulusan::find($id);
        
        if (!$kelulusan) {
            return ApiResponse::error('Data kelulusan tidak ditemukan', 404);
        }

        $request->validate([
            'id_mahasiswa'      => 'required|exists:data_mahasiswa,id',
            'tanggal_kelulusan' => 'required|date',
            'ipk'               => 'required|numeric|min:0|max:4',
            'masa_studi_bulan'  => 'required|integer|min:1',
        ]);

        $kelulusan->update($request->all());
        
        return ApiResponse::success($kelulusan, 'Data kelulusan berhasil diupdate');
    }

    public function destroy(string $id)
    {
        $kelulusan = DataKelulusan::find($id);
        
        if (!$kelulusan) {
            return ApiResponse::error('Data kelulusan tidak ditemukan', 404);
        }

        $kelulusan->delete();
        return ApiResponse::success(null, 'Data kelulusan berhasil dihapus');
    }
}