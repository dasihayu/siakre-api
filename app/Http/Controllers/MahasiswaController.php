<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\Mahasiswa;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class MahasiswaController extends Controller
{
    public function tren()
    {
        $years = ['2022', '2023', '2024', '2025', '2026'];
        $kelulusan = [];
        $ipkList = [];

        $ipkBenchmark = [
            '2022' => 3.30,
            '2023' => 3.40,
            '2024' => 3.52,
            '2025' => 3.52,
            '2026' => 3.52,
        ];

        foreach ($years as $year) {
            $intYear = (int) $year;
            $cohortYear = $intYear - 4;
            $cohort = Mahasiswa::where('tahun_masuk', $cohortYear)->get();
            $totalCohort = $cohort->count();

            if ($totalCohort > 0) {
                $onTimeCount = $cohort->filter(function ($m) {
                    return ! empty($m->tanggal_kelulusan) && (int) ($m->masa_studi_bulan ?? 0) <= 48;
                })->count();
                $pct = (int) round(($onTimeCount / $totalCohort) * 100);
            } else {
                $pct = 80;
            }

            $kelulusan[] = $pct;
            $ipkList[] = $ipkBenchmark[$year] ?? 3.50;
        }

        return ApiResponse::success([
            'years' => $years,
            'ipk' => $ipkList,
            'kelulusan' => $kelulusan,
        ], 'Berhasil mengambil tren IPK dan kelulusan');
    }

    public function index()
    {
        $data = Mahasiswa::all();

        return ApiResponse::success($data, 'Berhasil mengambil data Mahasiswa');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nim' => 'required|integer|unique:data_mahasiswa,nim',
            'nama_mahasiswa' => 'required|string',
            'tahun_masuk' => 'required|integer',
            'jenis_pendaftaran' => 'required|string',
            'tanggal_kelulusan' => 'nullable|date',
            'masa_studi_bulan' => 'required|integer',
        ], [
            'nim.unique' => 'NIM Mahasiswa Sudah Ada',
        ]);

        $mhs = Mahasiswa::create($request->all());

        return ApiResponse::success($mhs, 'Data Mahasiswa berhasil ditambahkan', 201);
    }

    public function show(string $id)
    {
        $mhs = Mahasiswa::find($id);

        if (! $mhs) {
            return ApiResponse::error('Data mahasiswa tidak ditemukan', 404);
        }

        return ApiResponse::success($mhs, 'Berhasil mengambil detail data Mahasiswa');
    }

    public function update(Request $request, string $id)
    {
        $mhs = Mahasiswa::find($id);

        if (! $mhs) {
            return ApiResponse::error('Data mahasiswa tidak ditemukan', 404);
        }

        $request->validate([
            'nim' => 'required|integer|unique:data_mahasiswa,nim,'.$id,
            'nama_mahasiswa' => 'required|string',
            'tahun_masuk' => 'required|integer',
            'jenis_pendaftaran' => 'required|string',
            'tanggal_kelulusan' => 'nullable|date',
            'masa_studi_bulan' => 'required|integer',
        ], [
            'nim.unique' => 'NIM Mahasiswa Sudah Ada',
        ]);

        $mhs->update($request->all());

        return ApiResponse::success($mhs, 'Data Mahasiswa berhasil diupdate');
    }

    public function destroy(string $id)
    {
        $mhs = Mahasiswa::find($id);

        if (! $mhs) {
            return ApiResponse::error('Data mahasiswa tidak ditemukan', 404);
        }

        try {
            $mhs->delete();

            return ApiResponse::success(null, 'Data mahasiswa berhasil dihapus');

        } catch (QueryException $e) {
            return ApiResponse::error('Gagal menghapus data karena masih terikat relasi', 400);
        }
    }
}
