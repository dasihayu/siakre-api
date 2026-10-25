<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\EnrollmentKelas;
use App\Models\Mahasiswa;
use App\Models\MataKuliahAngkatanKelas;
use App\Models\NilaiMahasiswa;
use App\Models\TahunAkademik;
use App\Services\AkademikCalculatorService;
use App\Services\SpreadsheetService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NilaiMahasiswaController extends Controller
{
    public function __construct(
        protected SpreadsheetService $spreadsheetService
    ) {}

    /**
     * Menampilkan seluruh data nilai mahasiswa (bisa difilter per kelas, mahasiswa, atau semester).
     */
    public function index(Request $request)
    {
        $query = NilaiMahasiswa::with([
            'mataKuliahAngkatanKelas.enrollmentMataKuliah.mataKuliah',
            'mataKuliahAngkatanKelas.enrollmentMataKuliah.tahunAkademik',
            'mataKuliahAngkatanKelas.angkatanKelas.kelas',
            'mahasiswa',
            'tahunAkademik',
        ]);

        if ($request->has('mata_kuliah_angkatan_kelas_id')) {
            $query->where('mata_kuliah_angkatan_kelas_id', $request->query('mata_kuliah_angkatan_kelas_id'));
        }

        if ($request->has('mahasiswa_id')) {
            $query->where('mahasiswa_id', $request->query('mahasiswa_id'));
        }

        if ($request->has('tahun_akademik_id')) {
            $query->where('tahun_akademik_id', $request->query('tahun_akademik_id'));
        }

        if ($request->has('semester')) {
            $semester = $request->query('semester');
            $semesterVal = match (strtolower((string) $semester)) {
                '1', '3', '5', '7', 'ganjil', 'gasal' => 'Ganjil',
                '2', '4', '6', '8', 'genap' => 'Genap',
                default => $semester,
            };

            $query->whereHas('tahunAkademik', function ($q) use ($semesterVal) {
                $q->where('semester', $semesterVal);
            });
        }

        $nilai = $query->get();

        return ApiResponse::success($nilai, 'Daftar nilai mahasiswa berhasil diambil');
    }

    /**
     * Menyimpan data nilai mahasiswa baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'mata_kuliah_angkatan_kelas_id' => 'required|integer|exists:mata_kuliah_angkatan_kelas,id',
            'mahasiswa_id' => 'required|integer|exists:data_mahasiswa,id',
            'tahun_akademik_id' => 'nullable|integer|exists:tahun_akademik,id',
            'nilai_angka' => 'nullable|numeric|min:0|max:100',
            'nilai_huruf' => 'nullable|string|max:5',
            'bobot' => 'nullable|numeric|min:0|max:4',
            'link_nilai' => 'nullable|string|max:2048',
            'link_evidence' => 'nullable|string|max:2048',
        ]);

        if (empty($validated['tahun_akademik_id'])) {
            $mkAk = MataKuliahAngkatanKelas::with('enrollmentMataKuliah')->find($validated['mata_kuliah_angkatan_kelas_id']);
            $validated['tahun_akademik_id'] = $mkAk?->enrollmentMataKuliah?->tahun_akademik_id
                ?? TahunAkademik::where('is_active', true)->first()?->id
                ?? TahunAkademik::first()?->id;
        }

        if (isset($validated['nilai_angka']) && empty($validated['nilai_huruf']) && ! isset($validated['bobot'])) {
            $calc = NilaiMahasiswa::hitungNilaiHurufDanBobot((float) $validated['nilai_angka']);
            $validated['nilai_huruf'] = $calc['nilai_huruf'];
            $validated['bobot'] = $calc['bobot'];
        }

        $nilai = NilaiMahasiswa::create($validated);
        $nilai->load(['mataKuliahAngkatanKelas', 'mahasiswa', 'tahunAkademik']);

        return ApiResponse::success($nilai, 'Nilai mahasiswa berhasil ditambahkan', 201);
    }

    /**
     * Menyimpan atau memperbarui nilai banyak mahasiswa sekaligus (Batch Input).
     */
    public function batchStore(Request $request)
    {
        $validated = $request->validate([
            'mata_kuliah_angkatan_kelas_id' => 'required|integer|exists:mata_kuliah_angkatan_kelas,id',
            'tahun_akademik_id' => 'nullable|integer|exists:tahun_akademik,id',
            'nilai' => 'required|array|min:1',
            'nilai.*.mahasiswa_id' => 'required|integer|exists:data_mahasiswa,id',
            'nilai.*.nilai_angka' => 'nullable|numeric|min:0|max:100',
            'nilai.*.nilai_huruf' => 'nullable|string|max:5',
            'nilai.*.bobot' => 'nullable|numeric|min:0|max:4',
            'nilai.*.link_nilai' => 'nullable|string|max:2048',
            'nilai.*.link_evidence' => 'nullable|string|max:2048',
        ]);

        $mkAkId = $validated['mata_kuliah_angkatan_kelas_id'];
        $tahunAkademikId = $validated['tahun_akademik_id'] ?? null;
        if (! $tahunAkademikId) {
            $mkAk = MataKuliahAngkatanKelas::with('enrollmentMataKuliah')->find($mkAkId);
            $tahunAkademikId = $mkAk?->enrollmentMataKuliah?->tahun_akademik_id
                ?? TahunAkademik::where('is_active', true)->first()?->id
                ?? TahunAkademik::first()?->id;
        }
        $results = [];

        foreach ($validated['nilai'] as $item) {
            $data = [
                'tahun_akademik_id' => $tahunAkademikId,
                'nilai_angka' => $item['nilai_angka'] ?? null,
                'nilai_huruf' => $item['nilai_huruf'] ?? null,
                'bobot' => $item['bobot'] ?? null,
                'link_nilai' => $item['link_nilai'] ?? null,
                'link_evidence' => $item['link_evidence'] ?? null,
            ];

            if (isset($data['nilai_angka']) && empty($data['nilai_huruf']) && ! isset($data['bobot'])) {
                $calc = NilaiMahasiswa::hitungNilaiHurufDanBobot((float) $data['nilai_angka']);
                $data['nilai_huruf'] = $calc['nilai_huruf'];
                $data['bobot'] = $calc['bobot'];
            }

            $nilai = NilaiMahasiswa::updateOrCreate(
                [
                    'mata_kuliah_angkatan_kelas_id' => $mkAkId,
                    'mahasiswa_id' => $item['mahasiswa_id'],
                ],
                $data
            );

            $results[] = $nilai;
        }

        return ApiResponse::success($results, 'Batch nilai mahasiswa berhasil disimpan', 201);
    }

    public function downloadTemplate(Request $request)
    {
        $format = strtolower($request->query('format', 'xlsx'));
        $mode = strtolower($request->query('mode', 'single'));
        $mkAkId = $request->query('mata_kuliah_angkatan_kelas_id');
        $kodeMk = $request->query('kode_mata_kuliah');

        $rows = [];

        if ($mode === 'multi') {
            $headers = [
                'Kode Mata Kuliah',
                'Nama Mata Kuliah',
                'Kelas',
                'NIM',
                'Nama Mahasiswa',
                'Nilai Angka',
                'Nilai Huruf',
                'Bobot',
                'Link Nilai',
                'Link Evidence',
            ];

            if ($kodeMk) {
                $mkList = MataKuliahAngkatanKelas::with([
                    'enrollmentMataKuliah.mataKuliah',
                    'angkatanKelas.kelas',
                ])->whereHas('enrollmentMataKuliah.mataKuliah', function ($q) use ($kodeMk) {
                    $q->where('kode_mata_kuliah', $kodeMk);
                })->get();

                foreach ($mkList as $mkItem) {
                    $cKode = $mkItem->enrollmentMataKuliah?->mataKuliah?->kode_mata_kuliah ?? $kodeMk;
                    $cNama = $mkItem->enrollmentMataKuliah?->mataKuliah?->nama_mata_kuliah ?? '';
                    $cKelas = $mkItem->angkatanKelas?->kelas?->kelas ?? 'Kelas';

                    $students = EnrollmentKelas::where('angkatan_kelas_id', $mkItem->angkatan_kelas_id)
                        ->with('mahasiswa')
                        ->get();

                    foreach ($students as $stu) {
                        $mhs = $stu->mahasiswa;
                        if ($mhs) {
                            $rows[] = [
                                $cKode,
                                $cNama,
                                $cKelas,
                                $mhs->nim,
                                $mhs->nama_mahasiswa,
                                '',
                                '',
                                '',
                                '',
                                '',
                            ];
                        }
                    }
                }
            }

            if (empty($rows)) {
                $rows = [
                    ['MK-NILAI-01', 'Basis Data Lanjut', 'TRK-3A', '43325001', 'Contoh Mahasiswa 1', 85.50, 'A', 4.00, 'https://docs.google.com/spreadsheets/d/sample1', 'https://drive.google.com/sample1'],
                    ['MK-NILAI-01', 'Basis Data Lanjut', 'TRK-3A', '43325002', 'Contoh Mahasiswa 2', 78.00, 'AB', 3.50, '', ''],
                    ['MK-NILAI-01', 'Basis Data Lanjut', 'TRK-3B', '43325020', 'Contoh Mahasiswa 3', 90.00, 'A', 4.00, '', ''],
                ];
            }
        } else {
            $headers = [
                'NIM',
                'Nama Mahasiswa',
                'Nilai Angka',
                'Nilai Huruf',
                'Bobot',
                'Link Nilai',
                'Link Evidence',
            ];

            if ($mkAkId) {
                $mkAk = MataKuliahAngkatanKelas::find($mkAkId);
                if ($mkAk) {
                    $students = EnrollmentKelas::where('angkatan_kelas_id', $mkAk->angkatan_kelas_id)
                        ->with('mahasiswa')
                        ->get();

                    foreach ($students as $stu) {
                        $mhs = $stu->mahasiswa;
                        if ($mhs) {
                            $rows[] = [
                                $mhs->nim,
                                $mhs->nama_mahasiswa,
                                '',
                                '',
                                '',
                                '',
                                '',
                            ];
                        }
                    }
                }
            }

            if (empty($rows)) {
                $rows = [
                    ['43325001', 'Contoh Mahasiswa 1', 85.50, 'A', 4.00, 'https://docs.google.com/spreadsheets/d/sample1', 'https://drive.google.com/sample1'],
                    ['43325002', 'Contoh Mahasiswa 2', 78.00, 'AB', 3.50, '', ''],
                ];
            }
        }

        if ($format === 'csv') {
            $content = $this->spreadsheetService->createCsv($headers, $rows);
            $filename = "template_nilai_{$mode}.csv";

            return response($content, 200, [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ]);
        }

        $content = $this->spreadsheetService->createXlsx($headers, $rows, 'Template Nilai');
        $filename = "template_nilai_{$mode}.xlsx";

        return response($content, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240',
            'mata_kuliah_angkatan_kelas_id' => 'nullable|integer|exists:mata_kuliah_angkatan_kelas,id',
            'tahun_akademik_id' => 'nullable|integer|exists:tahun_akademik,id',
            'duplicate_action' => 'nullable|string|in:update,skip,fail',
        ]);

        $file = $request->file('file');
        $ext = strtolower($file->getClientOriginalExtension());
        if (! in_array($ext, ['xlsx', 'csv', 'tsv', 'txt'])) {
            return ApiResponse::error('Format file tidak didukung. Harap unggah file .xlsx atau .csv', 422);
        }

        try {
            $rows = $this->spreadsheetService->parse($file);
        } catch (Exception $e) {
            return ApiResponse::error('Gagal membaca file: '.$e->getMessage(), 422);
        }

        if (empty($rows)) {
            return ApiResponse::error('File kosong atau tidak memiliki baris data untuk diimpor.', 422);
        }

        $duplicateAction = $request->input('duplicate_action', 'update');
        $fallbackMkAkId = $request->input('mata_kuliah_angkatan_kelas_id');
        $fallbackTahunAkademikId = $request->input('tahun_akademik_id');

        $importedCount = 0;
        $updatedCount = 0;
        $skippedCount = 0;
        $errors = [];
        $warnings = [];
        $processedInBatch = [];

        DB::beginTransaction();

        try {
            foreach ($rows as $row) {
                $rowNum = $row['_row_number'] ?? '?';
                $nim = trim((string) ($row['nim'] ?? ''));

                if ($nim === '') {
                    $errors[] = "Baris {$rowNum}: Kolom NIM wajib diisi.";

                    continue;
                }

                $mahasiswa = Mahasiswa::where('nim', $nim)->first();
                if (! $mahasiswa) {
                    $errors[] = "Baris {$rowNum}: Mahasiswa dengan NIM '{$nim}' tidak terdaftar di sistem.";

                    continue;
                }

                $mkAk = null;
                $rowMkAkId = $row['mata_kuliah_angkatan_kelas_id'] ?? null;
                if ($rowMkAkId) {
                    $mkAk = MataKuliahAngkatanKelas::with('enrollmentMataKuliah')->find($rowMkAkId);
                }

                if (! $mkAk) {
                    $kodeMk = trim((string) ($row['kode_mata_kuliah'] ?? ''));
                    $namaKelas = trim((string) ($row['kelas'] ?? ''));

                    if ($kodeMk !== '' && $namaKelas !== '') {
                        $mkQuery = MataKuliahAngkatanKelas::with('enrollmentMataKuliah')
                            ->whereHas('enrollmentMataKuliah.mataKuliah', function ($q) use ($kodeMk) {
                                $q->where('kode_mata_kuliah', $kodeMk);
                            })
                            ->whereHas('angkatanKelas.kelas', function ($q) use ($namaKelas) {
                                $q->where('kelas', $namaKelas);
                            });

                        if ($fallbackTahunAkademikId) {
                            $mkQuery->whereHas('enrollmentMataKuliah', fn ($q) => $q->where('tahun_akademik_id', $fallbackTahunAkademikId));
                        }

                        $mkAk = $mkQuery->first();

                        if (! $mkAk) {
                            $errors[] = "Baris {$rowNum}: Tidak ditemukan kelas '{$namaKelas}' untuk mata kuliah '{$kodeMk}'.";

                            continue;
                        }
                    }
                }

                if (! $mkAk && $fallbackMkAkId) {
                    $mkAk = MataKuliahAngkatanKelas::with('enrollmentMataKuliah')->find($fallbackMkAkId);
                }

                if (! $mkAk) {
                    $errors[] = "Baris {$rowNum}: Kelas dan mata kuliah tidak dapat diidentifikasi (isi kolom 'kode_mata_kuliah' & 'kelas', atau tentukan kelas pada form).";

                    continue;
                }

                $resolvedMkAkId = $mkAk->id;

                $tahunAkademikId = $fallbackTahunAkademikId
                    ?? $mkAk->enrollmentMataKuliah?->tahun_akademik_id
                    ?? TahunAkademik::where('is_active', true)->first()?->id
                    ?? TahunAkademik::first()?->id;

                $nilaiAngka = isset($row['nilai_angka']) && $row['nilai_angka'] !== '' ? (float) $row['nilai_angka'] : null;
                $nilaiHuruf = isset($row['nilai_huruf']) && $row['nilai_huruf'] !== '' ? strtoupper(trim((string) $row['nilai_huruf'])) : null;
                $bobot = isset($row['bobot']) && $row['bobot'] !== '' ? (float) $row['bobot'] : null;

                if ($nilaiAngka !== null && empty($nilaiHuruf) && $bobot === null) {
                    $calc = NilaiMahasiswa::hitungNilaiHurufDanBobot($nilaiAngka);
                    $nilaiHuruf = $calc['nilai_huruf'];
                    $bobot = $calc['bobot'];
                }

                $linkNilai = ! empty($row['link_nilai']) ? trim((string) $row['link_nilai']) : null;
                $linkEvidence = ! empty($row['link_evidence']) ? trim((string) $row['link_evidence']) : null;

                $batchKey = "{$resolvedMkAkId}_{$mahasiswa->id}";
                if (isset($processedInBatch[$batchKey])) {
                    if ($duplicateAction === 'fail') {
                        $errors[] = "Baris {$rowNum}: Terdapat duplikasi data untuk NIM '{$nim}' pada mata kuliah & kelas yang sama dalam file.";

                        continue;
                    } elseif ($duplicateAction === 'skip') {
                        $skippedCount++;
                        $warnings[] = "Baris {$rowNum}: Duplikasi NIM '{$nim}' dalam file, baris kedua dilewati.";

                        continue;
                    } else {
                        $warnings[] = "Baris {$rowNum}: Duplikasi NIM '{$nim}' dalam file, data diperbarui dengan baris terbaru.";
                    }
                }
                $processedInBatch[$batchKey] = true;

                $existingNilai = NilaiMahasiswa::where('mata_kuliah_angkatan_kelas_id', $resolvedMkAkId)
                    ->where('mahasiswa_id', $mahasiswa->id)
                    ->first();

                if ($existingNilai) {
                    if ($duplicateAction === 'fail') {
                        $errors[] = "Baris {$rowNum}: Nilai untuk NIM '{$nim}' sudah ada di database.";

                        continue;
                    } elseif ($duplicateAction === 'skip') {
                        $skippedCount++;
                        $warnings[] = "Baris {$rowNum}: Nilai NIM '{$nim}' sudah terdaftar sebelumnya di database, dilewati.";

                        continue;
                    } else {
                        $existingNilai->update([
                            'tahun_akademik_id' => $tahunAkademikId,
                            'nilai_angka' => $nilaiAngka ?? $existingNilai->nilai_angka,
                            'nilai_huruf' => $nilaiHuruf ?? $existingNilai->nilai_huruf,
                            'bobot' => $bobot ?? $existingNilai->bobot,
                            'link_nilai' => $linkNilai ?: $existingNilai->link_nilai,
                            'link_evidence' => $linkEvidence ?: $existingNilai->link_evidence,
                        ]);
                        $updatedCount++;

                        continue;
                    }
                }

                NilaiMahasiswa::create([
                    'mata_kuliah_angkatan_kelas_id' => $resolvedMkAkId,
                    'mahasiswa_id' => $mahasiswa->id,
                    'tahun_akademik_id' => $tahunAkademikId,
                    'nilai_angka' => $nilaiAngka,
                    'nilai_huruf' => $nilaiHuruf,
                    'bobot' => $bobot,
                    'link_nilai' => $linkNilai,
                    'link_evidence' => $linkEvidence,
                ]);
                $importedCount++;
            }

            if ($duplicateAction === 'fail' && ! empty($errors)) {
                DB::rollBack();

                return ApiResponse::error('Proses import dibatalkan karena ditemukan error atau duplikasi data.', 422, [
                    'total_rows' => count($rows),
                    'errors' => $errors,
                ]);
            }

            DB::commit();

            return ApiResponse::success([
                'total_rows' => count($rows),
                'imported_count' => $importedCount,
                'updated_count' => $updatedCount,
                'skipped_count' => $skippedCount,
                'failed_count' => count($errors),
                'errors' => $errors,
                'warnings' => $warnings,
            ], 'Proses import nilai mahasiswa selesai diproses');
        } catch (Exception $e) {
            DB::rollBack();

            return ApiResponse::error('Terjadi kesalahan saat memproses data import: '.$e->getMessage(), 500);
        }
    }

    /**
     * Menampilkan detail data nilai mahasiswa spesifik.
     */
    public function show(string $id)
    {
        $nilai = NilaiMahasiswa::with([
            'mataKuliahAngkatanKelas.enrollmentMataKuliah.mataKuliah',
            'mataKuliahAngkatanKelas.enrollmentMataKuliah.tahunAkademik',
            'mataKuliahAngkatanKelas.angkatanKelas.kelas',
            'mahasiswa',
            'tahunAkademik',
        ])->find($id);

        if (! $nilai) {
            return ApiResponse::error('Data nilai mahasiswa tidak ditemukan', 404);
        }

        return ApiResponse::success($nilai, 'Detail data nilai mahasiswa berhasil diambil');
    }

    /**
     * Memperbarui seluruh/sebagian data nilai mahasiswa.
     */
    public function update(Request $request, string $id)
    {
        $nilai = NilaiMahasiswa::find($id);

        if (! $nilai) {
            return ApiResponse::error('Data nilai mahasiswa tidak ditemukan', 404);
        }

        $validated = $request->validate([
            'mata_kuliah_angkatan_kelas_id' => 'sometimes|required|integer|exists:mata_kuliah_angkatan_kelas,id',
            'mahasiswa_id' => 'sometimes|required|integer|exists:data_mahasiswa,id',
            'tahun_akademik_id' => 'sometimes|required|integer|exists:tahun_akademik,id',
            'nilai_angka' => 'nullable|numeric|min:0|max:100',
            'nilai_huruf' => 'nullable|string|max:5',
            'bobot' => 'nullable|numeric|min:0|max:4',
            'link_nilai' => 'nullable|string|max:2048',
            'link_evidence' => 'nullable|string|max:2048',
        ]);

        if (isset($validated['nilai_angka']) && empty($validated['nilai_huruf']) && ! isset($validated['bobot'])) {
            $calc = NilaiMahasiswa::hitungNilaiHurufDanBobot((float) $validated['nilai_angka']);
            $validated['nilai_huruf'] = $calc['nilai_huruf'];
            $validated['bobot'] = $calc['bobot'];
        }

        $nilai->update($validated);
        $nilai->load(['mataKuliahAngkatanKelas', 'mahasiswa', 'tahunAkademik']);

        return ApiResponse::success($nilai, 'Data nilai mahasiswa berhasil diperbarui');
    }

    /**
     * Menghapus data nilai mahasiswa.
     */
    public function destroy(string $id)
    {
        $nilai = NilaiMahasiswa::find($id);

        if (! $nilai) {
            return ApiResponse::error('Data nilai mahasiswa tidak ditemukan', 404);
        }

        $nilai->delete();

        return ApiResponse::success(null, 'Data nilai mahasiswa berhasil dihapus');
    }

    /**
     * Endpoint khusus untuk mengupdate link_nilai mahasiswa.
     */
    public function updateLinkNilai(Request $request, string $id)
    {
        $nilai = NilaiMahasiswa::find($id);

        if (! $nilai) {
            return ApiResponse::error('Data nilai mahasiswa tidak ditemukan', 404);
        }

        $validated = $request->validate([
            'link_nilai' => 'required|string|max:2048',
        ]);

        $nilai->update([
            'link_nilai' => $validated['link_nilai'],
        ]);

        return ApiResponse::success($nilai, 'Link nilai mahasiswa berhasil diperbarui');
    }

    /**
     * Endpoint khusus untuk mengupdate link_evidence mahasiswa.
     */
    public function updateLinkEvidence(Request $request, string $id)
    {
        $nilai = NilaiMahasiswa::find($id);

        if (! $nilai) {
            return ApiResponse::error('Data nilai mahasiswa tidak ditemukan', 404);
        }

        $validated = $request->validate([
            'link_evidence' => 'required|string|max:2048',
        ]);

        $nilai->update([
            'link_evidence' => $validated['link_evidence'],
        ]);

        return ApiResponse::success($nilai, 'Link evidence mahasiswa berhasil diperbarui');
    }

    /**
     * Menampilkan Kartu Hasil Studi (KHS) mahasiswa per semester.
     * Menggunakan rumus baku: IP = SUM(SKS_i * Bobot_i) / SUM(SKS_i).
     */
    public function khs(Request $request, string $id)
    {
        $mahasiswa = Mahasiswa::find($id);

        if (! $mahasiswa) {
            return ApiResponse::error('Data mahasiswa tidak ditemukan', 404);
        }

        $tahunAkademikId = $request->query('tahun_akademik_id');
        $semester = $request->query('semester');

        $query = NilaiMahasiswa::with([
            'mataKuliahAngkatanKelas.enrollmentMataKuliah.mataKuliah',
            'mataKuliahAngkatanKelas.enrollmentMataKuliah.tahunAkademik',
            'tahunAkademik',
        ])->where('mahasiswa_id', $id);

        if ($tahunAkademikId) {
            $query->where('tahun_akademik_id', $tahunAkademikId);
        }

        if ($semester) {
            $semesterVal = match (strtolower((string) $semester)) {
                '1', '3', '5', '7', 'ganjil', 'gasal' => 'Ganjil',
                '2', '4', '6', '8', 'genap' => 'Genap',
                default => $semester,
            };

            $query->whereHas('tahunAkademik', function ($q) use ($semesterVal) {
                $q->where('semester', $semesterVal);
            });
        }

        $nilaiList = $query->get();

        $calc = AkademikCalculatorService::hitungIP($nilaiList);

        $items = $nilaiList->map(function ($item) {
            $mk = $item->mataKuliahAngkatanKelas?->enrollmentMataKuliah?->mataKuliah;
            $sks = $mk?->sks ?? 0;
            $bobot = $item->bobot ?? 0;
            $kredit = round($sks * $bobot, 2);

            return [
                'id' => $item->id,
                'tahun_akademik_id' => $item->tahun_akademik_id,
                'tahun_akademik' => $item->tahunAkademik?->tahun,
                'semester' => $item->semester,
                'kode_mata_kuliah' => $mk?->kode_mata_kuliah,
                'nama_mata_kuliah' => $mk?->nama_mata_kuliah,
                'sks' => $sks,
                'nilai_angka' => $item->nilai_angka,
                'nilai_huruf' => $item->nilai_huruf,
                'bobot' => $bobot,
                'kredit' => $kredit,
                'link_nilai' => $item->link_nilai,
                'link_evidence' => $item->link_evidence,
            ];
        });

        return ApiResponse::success([
            'mahasiswa' => [
                'id' => $mahasiswa->id,
                'nim' => $mahasiswa->nim,
                'nama' => $mahasiswa->nama_mahasiswa,
            ],
            'tahun_akademik_id' => $tahunAkademikId ? (int) $tahunAkademikId : null,
            'semester' => $semester ?? 'Semua',
            'total_sks' => $calc['total_sks'],
            'total_kredit' => $calc['total_kredit'],
            'ip' => $calc['ip'],
            'ips' => $calc['ip'],
            'daftar_nilai' => $items,
        ], 'Kartu Hasil Studi (KHS) berhasil diambil');
    }

    /**
     * Menampilkan Transkrip Nilai Kumulatif mahasiswa.
     * Menggunakan rumus baku: IPK = SUM(SKS_i * Bobot_i) / SUM(SKS_i).
     * Mendukung evaluasi mata kuliah mengulang (rule_mengulang: 'terbaik' / 'terbaru' / 'semua').
     */
    public function transkrip(Request $request, string $id)
    {
        $mahasiswa = Mahasiswa::find($id);

        if (! $mahasiswa) {
            return ApiResponse::error('Data mahasiswa tidak ditemukan', 404);
        }

        $ruleMengulang = $request->query('rule_mengulang', 'terbaik');

        $nilaiList = NilaiMahasiswa::with([
            'mataKuliahAngkatanKelas.enrollmentMataKuliah.mataKuliah',
            'mataKuliahAngkatanKelas.enrollmentMataKuliah.tahunAkademik',
            'tahunAkademik',
        ])
            ->where('mahasiswa_id', $id)
            ->get()
            ->sortBy([
                ['tahunAkademik.tahun', 'asc'],
                ['tahunAkademik.semester', 'asc'],
            ]);

        $ipkResult = AkademikCalculatorService::hitungIPK($nilaiList, $ruleMengulang);

        return ApiResponse::success([
            'mahasiswa' => [
                'id' => $mahasiswa->id,
                'nim' => $mahasiswa->nim,
                'nama' => $mahasiswa->nama_mahasiswa,
            ],
            'total_sks' => $ipkResult['total_sks'],
            'total_kredit' => $ipkResult['total_kredit'],
            'ipk' => $ipkResult['ipk'],
            'rule_mengulang' => $ruleMengulang,
            'daftar_nilai' => $ipkResult['items'],
        ], 'Transkrip nilai mahasiswa berhasil diambil');
    }

    /**
     * Menampilkan ringkasan perkembangan akademik per semester (IP tiap semester dan IPK kumulatif).
     */
    public function ringkasanAkademik(string $id)
    {
        $mahasiswa = Mahasiswa::find($id);

        if (! $mahasiswa) {
            return ApiResponse::error('Data mahasiswa tidak ditemukan', 404);
        }

        $nilaiList = NilaiMahasiswa::with([
            'mataKuliahAngkatanKelas.enrollmentMataKuliah.mataKuliah',
            'mataKuliahAngkatanKelas.enrollmentMataKuliah.tahunAkademik',
            'tahunAkademik',
        ])
            ->where('mahasiswa_id', $id)
            ->get()
            ->sortBy([
                ['tahunAkademik.tahun', 'asc'],
                ['tahunAkademik.semester', 'asc'],
            ]);

        $grouped = $nilaiList->groupBy('tahun_akademik_id');

        $riwayatSemester = [];
        $runningItems = [];
        $semesterKe = 1;

        foreach ($grouped as $taId => $itemsInSemester) {
            $ta = $itemsInSemester->first()?->tahunAkademik;
            $namaPeriode = $ta ? "{$ta->tahun} - {$ta->semester}" : "Periode ID {$taId}";

            $calcSemester = AkademikCalculatorService::hitungIP($itemsInSemester);

            foreach ($itemsInSemester as $item) {
                $runningItems[] = $item;
            }

            $calcKumulatif = AkademikCalculatorService::hitungIPK($runningItems, 'terbaik');

            $riwayatSemester[] = [
                'semester_ke' => $semesterKe++,
                'tahun_akademik_id' => $taId,
                'tahun' => $ta?->tahun,
                'semester' => $ta?->semester,
                'nama_periode' => $namaPeriode,
                'sks_semester' => $calcSemester['total_sks'],
                'kredit_semester' => $calcSemester['total_kredit'],
                'ip_semester' => $calcSemester['ip'],
                'sks_kumulatif' => $calcKumulatif['total_sks'],
                'kredit_kumulatif' => $calcKumulatif['total_kredit'],
                'ipk_kumulatif' => $calcKumulatif['ipk'],
            ];
        }

        $overallIpk = AkademikCalculatorService::hitungIPK($nilaiList, 'terbaik');

        return ApiResponse::success([
            'mahasiswa' => [
                'id' => $mahasiswa->id,
                'nim' => $mahasiswa->nim,
                'nama' => $mahasiswa->nama_mahasiswa,
                'tahun_masuk' => $mahasiswa->tahun_masuk,
            ],
            'total_sks' => $overallIpk['total_sks'],
            'total_kredit' => $overallIpk['total_kredit'],
            'ipk' => $overallIpk['ipk'],
            'predikat' => match (true) {
                $overallIpk['ipk'] >= 3.51 => 'Dengan Pujian (Cum Laude)',
                $overallIpk['ipk'] >= 3.01 => 'Sangat Memuaskan',
                $overallIpk['ipk'] >= 2.76 => 'Memuaskan',
                default => 'Cukup',
            },
            'perkembangan_semester' => $riwayatSemester,
        ], 'Ringkasan akademik mahasiswa berhasil diambil');
    }
}
