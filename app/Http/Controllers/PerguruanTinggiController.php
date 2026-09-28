<?php

namespace App\Http\Controllers;

use App\Models\PerguruanTinggi;
use Illuminate\Http\Request;

class PerguruanTinggiController extends Controller
{
    // Tampil semua data (Read)
    public function index()
    {
        $data = PerguruanTinggi::all();
        return response()->json(['message' => 'Berhasil mengambil data', 'data' => $data]);
    }

    // Tambah data (Create)
    public function store(Request $request)
    {
        // Validasi dasar
        $request->validate([
            'kode_pt' => 'required|string',
            'nama_pt' => 'required|string',
        ]);

        $pt = PerguruanTinggi::create($request->all());
        return response()->json(['message' => 'Data Perguruan Tinggi berhasil ditambahkan', 'data' => $pt], 201);
    }

    // Tampil satu data spesifik (Read Detail)
    public function show(string $id)
    {
        $pt = PerguruanTinggi::find($id);
        if (!$pt) return response()->json(['message' => 'Data tidak ditemukan'], 404);
        
        return response()->json(['message' => 'Berhasil mengambil detail data', 'data' => $pt]);
    }

    // Update data
    public function update(Request $request, string $id)
    {
        $pt = PerguruanTinggi::find($id);
        if (!$pt) return response()->json(['message' => 'Data tidak ditemukan'], 404);

        $pt->update($request->all());
        return response()->json(['message' => 'Data berhasil diupdate', 'data' => $pt]);
    }

    // Hapus data (Delete)
    public function destroy(string $id)
    {
        $pt = PerguruanTinggi::find($id);
        if (!$pt) return response()->json(['message' => 'Data tidak ditemukan'], 404);

        $pt->delete();
        return response()->json(['message' => 'Data berhasil dihapus']);
    }
}