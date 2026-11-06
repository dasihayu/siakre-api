<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\EvidenceIndikator;
use App\Models\IndikatorLam;
use App\Models\KegiatanMahasiswa;
use App\Models\NilaiMahasiswa;
use App\Models\PrestasiMahasiswa;
use Illuminate\Http\Request;

class EvidenceIndikatorController extends Controller
{
    /**
     * GET /api/evidence-indikator/{id_indikator}
     *
     * Menarik semua bukti (evidence) yang terkait dengan suatu indikator LAM INFOKOM.
     * Mendukung filter opsional: tipe_sumber.
     */
    public function show(Request $request, string $idIndikator)
    {
        $indikator = IndikatorLam::find($idIndikator);

        if (! $indikator) {
            return ApiResponse::error('Indikator LAM tidak ditemukan', 404);
        }

        $query = EvidenceIndikator::where('id_indikator_lam', $idIndikator);

        if ($request->filled('tipe_sumber')) {
            $query->where('tipe_sumber', $request->query('tipe_sumber'));
        }

        $evidenceList = $query->get();

        // Kelompokkan id per tipe sumber untuk eager loading efisien
        $idsByType = $evidenceList->groupBy('tipe_sumber')->map(
            fn ($items) => $items->pluck('id_sumber')->all()
        );

        $nilaiMap = isset($idsByType['nilai_mahasiswa'])
            ? NilaiMahasiswa::with([
                'mataKuliahAngkatanKelas.enrollmentMataKuliah.mataKuliah',
                'mahasiswa:id,nim,nama_mahasiswa',
                'tahunAkademik:id,tahun,semester',
            ])->findMany($idsByType['nilai_mahasiswa'])->keyBy('id')
            : collect();

        $prestasiMap = isset($idsByType['prestasi_mahasiswa'])
            ? PrestasiMahasiswa::with([
                'mahasiswa:id,nim,nama_mahasiswa',
                'evidence',
                'jenisPrestasi',
            ])->findMany($idsByType['prestasi_mahasiswa'])->keyBy('id')
            : collect();

        $kegiatanMap = isset($idsByType['kegiatan_mahasiswa'])
            ? KegiatanMahasiswa::with([
                'mahasiswa:id,nim,nama_mahasiswa',
                'tahunAkademik:id,tahun,semester',
            ])->findMany($idsByType['kegiatan_mahasiswa'])->keyBy('id')
            : collect();

        $result = $evidenceList->map(function (EvidenceIndikator $ev) use ($nilaiMap, $prestasiMap, $kegiatanMap) {
            $sumber = match ($ev->tipe_sumber) {
                'nilai_mahasiswa' => $nilaiMap->get($ev->id_sumber),
                'prestasi_mahasiswa' => $prestasiMap->get($ev->id_sumber),
                'kegiatan_mahasiswa' => $kegiatanMap->get($ev->id_sumber),
                default => null,
            };

            return [
                'id' => $ev->id,
                'tipe_sumber' => $ev->tipe_sumber,
                'id_sumber' => $ev->id_sumber,
                'detail_sumber' => $sumber,
            ];
        });

        return ApiResponse::success([
            'indikator' => [
                'id' => $indikator->id,
                'kode_indikator' => $indikator->kode_indikator,
                'nama_indikator' => $indikator->nama_indikator,
                'standar' => $indikator->standar,
            ],
            'total_evidence' => $result->count(),
            'evidence' => $result->values(),
        ], 'Berhasil mengambil evidence untuk indikator');
    }

    /**
     * POST /api/evidence-indikator
     *
     * Memetakan sebuah sumber evidence ke indikator LAM INFOKOM.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipe_sumber' => 'required|in:nilai_mahasiswa,prestasi_mahasiswa,kegiatan_mahasiswa',
            'id_sumber' => 'required|integer|min:1',
            'id_indikator_lam' => 'required|exists:indikator_lam,id',
        ]);

        // Pastikan id_sumber ada di tabel yang sesuai
        $this->validasiIdSumber($validated['tipe_sumber'], $validated['id_sumber']);

        $mapping = EvidenceIndikator::firstOrCreate(
            [
                'tipe_sumber' => $validated['tipe_sumber'],
                'id_sumber' => $validated['id_sumber'],
                'id_indikator_lam' => $validated['id_indikator_lam'],
            ]
        );

        return ApiResponse::success($mapping, 'Pemetaan evidence berhasil disimpan', $mapping->wasRecentlyCreated ? 201 : 200);
    }

    /**
     * DELETE /api/evidence-indikator/{id}
     */
    public function destroy(string $id)
    {
        $mapping = EvidenceIndikator::find($id);

        if (! $mapping) {
            return ApiResponse::error('Pemetaan tidak ditemukan', 404);
        }

        $mapping->delete();

        return ApiResponse::success(null, 'Pemetaan evidence berhasil dihapus');
    }

    /**
     * Validasi bahwa id_sumber ada di tabel yang bersesuaian.
     */
    private function validasiIdSumber(string $tipe, int $idSumber): void
    {
        $exists = match ($tipe) {
            'nilai_mahasiswa' => NilaiMahasiswa::where('id', $idSumber)->exists(),
            'prestasi_mahasiswa' => PrestasiMahasiswa::where('id', $idSumber)->exists(),
            'kegiatan_mahasiswa' => KegiatanMahasiswa::where('id', $idSumber)->exists(),
            default => false,
        };

        if (! $exists) {
            abort(422, "id_sumber {$idSumber} tidak ditemukan pada tabel {$tipe}.");
        }
    }
}
