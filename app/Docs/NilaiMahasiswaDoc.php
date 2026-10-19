<?php

namespace App\Docs;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Nilai Mahasiswa', description: 'Endpoint API Nilai Mahasiswa')]
class NilaiMahasiswaDoc
{
    #[OA\Get(
        path: '/api/nilai-mahasiswa',
        summary: 'Daftar Semua Data Nilai Mahasiswa',
        description: '### 📋 1. Request Headers
| Header | Tipe Data | Wajib / Required | Keterangan | Contoh Nilai |
| :--- | :--- | :--- | :--- | :--- |
| `Authorization` | `string` | **Ya (Required)** | Token Bearer JWT hasil otentikasi login | `Bearer eyJhbGci...` |
| `Accept` | `string` | **Ya (Required)** | Tipe konten respon server yang diharapkan | `application/json` |

### 📤 2. Struktur Response
| Status HTTP | Field / Properti | Tipe Data | Wajib / Required | Keterangan | Contoh Nilai |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **200 OK** | `success` | `boolean` | **Ya (Required)** | Indikator status keberhasilan (`true`) | `true` |
| | `code` | `integer` | **Ya (Required)** | HTTP Status Code (200) | `200` |
| | `message` | `string` | **Ya (Required)** | Pesan informasi respon server | "Operasi berhasil" |
| | `data` | `array[object]` | **Ya (Required)** | Koleksi daftar data entitas | `[ { ... } ]` |
| | `data[].id` | `integer` | **Ya (Required)** | Id | `104` |
| | `data[].enrollment_mahasiswa_angkatan_kelas_id` | `integer` | **Ya (Required)** | Enrollment Mahasiswa Angkatan Kelas Id | `12` |
| | `data[].mata_kuliah_angkatan_kelas_id` | `integer` | **Ya (Required)** | Mata Kuliah Angkatan Kelas Id | `93` |
| | `data[].nilai_angka` | `number` | **Ya (Required)** | Nilai Angka | `94.5` |
| | `data[].nilai_huruf` | `string` | **Ya (Required)** | Nilai Huruf | `"A"` |
| | `data[].bobot_nilai` | `number` | **Ya (Required)** | Bobot Nilai | `4` |
| | `data[].status_kelulusan` | `string` | **Ya (Required)** | Status Kelulusan | `"Lulus"` |
| | `data[].created_at` | `string` | **Ya (Required)** | Created At | `"2026-10-19 03:56:52"` |
| | `data[].updated_at` | `string` | **Ya (Required)** | Updated At | `"2026-10-19 03:56:52"` |
| **401 Unauthorized** | `message` | `string` | **Ya (Required)** | Pesan otentikasi gagal saat token kosong atau kedaluwarsa | \\"Unauthenticated.\\" |
| **403 Forbidden** | `message` | `string` | **Ya (Required)** | Pesan penolakan izin akses Role/Permission | \\"User does not have the right permissions.\\" |
| **400 Bad Request** | `message` | `string` | **Ya (Required)** | Permintaan tidak valid atau data berelasi masih terikat | \\"Permintaan tidak valid\\" |',
        tags: ['Nilai Mahasiswa'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Operasi berhasil'),
            new OA\Response(response: 400, description: 'Permintaan tidak valid / Data terkait masih digunakan'),
            new OA\Response(response: 401, description: 'Unauthenticated / Token tidak valid'),
            new OA\Response(response: 403, description: 'Forbidden / Tidak memiliki hak akses'),
            new OA\Response(response: 404, description: 'Data tidak ditemukan'),
            new OA\Response(response: 422, description: 'Validasi entitas gagal'),
        ]
    )]
    public function index(): void {}

    #[OA\Post(
        path: '/api/nilai-mahasiswa',
        summary: 'Tambah Data Nilai Mahasiswa Baru',
        description: '### 📋 1. Request Headers
| Header | Tipe Data | Wajib / Required | Keterangan | Contoh Nilai |
| :--- | :--- | :--- | :--- | :--- |
| `Authorization` | `string` | **Ya (Required)** | Token Bearer JWT hasil otentikasi login | `Bearer eyJhbGci...` |
| `Content-Type` | `string` | **Ya (Required)** | Tipe konten payload body permintaan | `application/json` |
| `Accept` | `string` | **Ya (Required)** | Tipe konten respon server yang diharapkan | `application/json` |

### 📥 2. Request Body
| Parameter | Tipe Data | Wajib / Required | Keterangan | Contoh Riil |
| :--- | :--- | :--- | :--- | :--- |
| `enrollment_mahasiswa_angkatan_kelas_id` | `integer` | **Ya (Required)** | ID referensi enrollment mahasiswa pada angkatan kelas | `12` |
| `mata_kuliah_angkatan_kelas_id` | `integer` | **Ya (Required)** | ID referensi penawaran mata kuliah pada angkatan kelas | `93` |
| `nilai_angka` | `number` | **Ya (Required)** | Nilai capaian akademik mahasiswa dalam format angka (0.00 - 100.00) | `94.5` |
| `nilai_huruf` | `string` | **Ya (Required)** | Indeks prestasi huruf mutu (A, AB, B, BC, C, D, E) | `A` |
| `bobot_nilai` | `number` | **Ya (Required)** | Bobot angka mutu kredit akademik (4.00, 3.50, 3.00, 2.50, 2.00, 1.00, 0.00) | `4` |
| `status_kelulusan` | `string` | **Ya (Required)** | Status kelulusan mata kuliah (Lulus / Tidak Lulus) | `Lulus` |

### 📤 3. Struktur Response
| Status HTTP | Field / Properti | Tipe Data | Wajib / Required | Keterangan | Contoh Nilai |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **201 Created** | `success` | `boolean` | **Ya (Required)** | Indikator status keberhasilan (`true`) | `true` |
| | `code` | `integer` | **Ya (Required)** | HTTP Status Code (201) | `201` |
| | `message` | `string` | **Ya (Required)** | Pesan informasi respon server | "Data berhasil dibuat" |
| | `data` | `object` | **Ya (Required)** | Objek data entitas hasil operasi | `{ ... }` |
| | `data.id` | `integer` | **Ya (Required)** | Id | `104` |
| | `data.enrollment_mahasiswa_angkatan_kelas_id` | `integer` | **Ya (Required)** | ID referensi enrollment mahasiswa pada angkatan kelas | `12` |
| | `data.mata_kuliah_angkatan_kelas_id` | `integer` | **Ya (Required)** | ID referensi penawaran mata kuliah pada angkatan kelas | `93` |
| | `data.nilai_angka` | `number` | **Ya (Required)** | Nilai capaian akademik mahasiswa dalam format angka (0.00 - 100.00) | `94.5` |
| | `data.nilai_huruf` | `string` | **Ya (Required)** | Indeks prestasi huruf mutu (A, AB, B, BC, C, D, E) | `"A"` |
| | `data.bobot_nilai` | `number` | **Ya (Required)** | Bobot angka mutu kredit akademik (4.00, 3.50, 3.00, 2.50, 2.00, 1.00, 0.00) | `4` |
| | `data.status_kelulusan` | `string` | **Ya (Required)** | Status kelulusan mata kuliah (Lulus / Tidak Lulus) | `"Lulus"` |
| | `data.created_at` | `string` | **Ya (Required)** | Created At | `"2026-10-19 03:56:52"` |
| | `data.updated_at` | `string` | **Ya (Required)** | Updated At | `"2026-10-19 03:56:52"` |
| **422 Unprocessable** | `success` | `boolean` | **Ya (Required)** | Indikator kegagalan validasi (`false`) | `false` |
| | `code` | `integer` | **Ya (Required)** | HTTP Status Code (422) | `422` |
| | `message` | `string` | **Ya (Required)** | Ringkasan pesan kegagalan validasi | \\"The given data was invalid.\\" |
| | `errors` | `object` | **Ya (Required)** | Kamus pesan kesalahan validasi per field input | `{\\"field\\": [\\"Pesan error\\"]}` |
| **401 Unauthorized** | `message` | `string` | **Ya (Required)** | Pesan otentikasi gagal saat token kosong atau kedaluwarsa | \\"Unauthenticated.\\" |
| **403 Forbidden** | `message` | `string` | **Ya (Required)** | Pesan penolakan izin akses Role/Permission | \\"User does not have the right permissions.\\" |
| **400 Bad Request** | `message` | `string` | **Ya (Required)** | Permintaan tidak valid atau data berelasi masih terikat | \\"Permintaan tidak valid\\" |',
        tags: ['Nilai Mahasiswa'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['enrollment_mahasiswa_angkatan_kelas_id', 'mata_kuliah_angkatan_kelas_id', 'nilai_angka', 'nilai_huruf', 'bobot_nilai', 'status_kelulusan'],
                properties: [
                    new OA\Property(property: 'enrollment_mahasiswa_angkatan_kelas_id', type: 'integer', example: 12, description: 'ID referensi enrollment mahasiswa pada angkatan kelas'),
                    new OA\Property(property: 'mata_kuliah_angkatan_kelas_id', type: 'integer', example: 93, description: 'ID referensi penawaran mata kuliah pada angkatan kelas'),
                    new OA\Property(property: 'nilai_angka', type: 'number', example: 94.5, description: 'Nilai capaian akademik mahasiswa dalam format angka (0.00 - 100.00)'),
                    new OA\Property(property: 'nilai_huruf', type: 'string', example: 'A', description: 'Indeks prestasi huruf mutu (A, AB, B, BC, C, D, E)'),
                    new OA\Property(property: 'bobot_nilai', type: 'number', example: 4, description: 'Bobot angka mutu kredit akademik (4.00, 3.50, 3.00, 2.50, 2.00, 1.00, 0.00)'),
                    new OA\Property(property: 'status_kelulusan', type: 'string', example: 'Lulus', description: 'Status kelulusan mata kuliah (Lulus / Tidak Lulus)'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Data berhasil dibuat'),
            new OA\Response(response: 400, description: 'Permintaan tidak valid / Data terkait masih digunakan'),
            new OA\Response(response: 401, description: 'Unauthenticated / Token tidak valid'),
            new OA\Response(response: 403, description: 'Forbidden / Tidak memiliki hak akses'),
            new OA\Response(response: 404, description: 'Data tidak ditemukan'),
            new OA\Response(response: 422, description: 'Validasi entitas gagal'),
        ]
    )]
    public function store(): void {}

    #[OA\Get(
        path: '/api/nilai-mahasiswa/{id}',
        summary: 'Detail Data Nilai Mahasiswa',
        description: '### 📋 1. Request Headers
| Header | Tipe Data | Wajib / Required | Keterangan | Contoh Nilai |
| :--- | :--- | :--- | :--- | :--- |
| `Authorization` | `string` | **Ya (Required)** | Token Bearer JWT hasil otentikasi login | `Bearer eyJhbGci...` |
| `Accept` | `string` | **Ya (Required)** | Tipe konten respon server yang diharapkan | `application/json` |

### 🔍 2. Path Parameters
| Parameter | Tipe Data | Wajib / Required | Keterangan | Contoh Nilai |
| :--- | :--- | :--- | :--- | :--- |
| `id` | `integer` | **Ya (Required)** | Parameter ID unik id pada path URL | `1` |

### 📤 3. Struktur Response
| Status HTTP | Field / Properti | Tipe Data | Wajib / Required | Keterangan | Contoh Nilai |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **200 OK** | `success` | `boolean` | **Ya (Required)** | Indikator status keberhasilan (`true`) | `true` |
| | `code` | `integer` | **Ya (Required)** | HTTP Status Code (200) | `200` |
| | `message` | `string` | **Ya (Required)** | Pesan informasi respon server | "Operasi berhasil" |
| | `data` | `object` | **Ya (Required)** | Objek data entitas hasil operasi | `{ ... }` |
| | `data.id` | `integer` | **Ya (Required)** | Id | `104` |
| | `data.enrollment_mahasiswa_angkatan_kelas_id` | `integer` | **Ya (Required)** | Enrollment Mahasiswa Angkatan Kelas Id | `12` |
| | `data.mata_kuliah_angkatan_kelas_id` | `integer` | **Ya (Required)** | Mata Kuliah Angkatan Kelas Id | `93` |
| | `data.nilai_angka` | `number` | **Ya (Required)** | Nilai Angka | `94.5` |
| | `data.nilai_huruf` | `string` | **Ya (Required)** | Nilai Huruf | `"A"` |
| | `data.bobot_nilai` | `number` | **Ya (Required)** | Bobot Nilai | `4` |
| | `data.status_kelulusan` | `string` | **Ya (Required)** | Status Kelulusan | `"Lulus"` |
| | `data.created_at` | `string` | **Ya (Required)** | Created At | `"2026-10-19 03:56:52"` |
| | `data.updated_at` | `string` | **Ya (Required)** | Updated At | `"2026-10-19 03:56:52"` |
| **401 Unauthorized** | `message` | `string` | **Ya (Required)** | Pesan otentikasi gagal saat token kosong atau kedaluwarsa | \\"Unauthenticated.\\" |
| **403 Forbidden** | `message` | `string` | **Ya (Required)** | Pesan penolakan izin akses Role/Permission | \\"User does not have the right permissions.\\" |
| **404 Not Found** | `message` | `string` | **Ya (Required)** | Data referensi dengan ID yang diminta tidak ditemukan di database | \\"Data tidak ditemukan\\" |
| **400 Bad Request** | `message` | `string` | **Ya (Required)** | Permintaan tidak valid atau data berelasi masih terikat | \\"Permintaan tidak valid\\" |',
        tags: ['Nilai Mahasiswa'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID data id',
                schema: new OA\Schema(type: 'integer', example: 1)
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Operasi berhasil'),
            new OA\Response(response: 400, description: 'Permintaan tidak valid / Data terkait masih digunakan'),
            new OA\Response(response: 401, description: 'Unauthenticated / Token tidak valid'),
            new OA\Response(response: 403, description: 'Forbidden / Tidak memiliki hak akses'),
            new OA\Response(response: 404, description: 'Data tidak ditemukan'),
            new OA\Response(response: 422, description: 'Validasi entitas gagal'),
        ]
    )]
    public function show(): void {}

    #[OA\Put(
        path: '/api/nilai-mahasiswa/{id}',
        summary: 'Perbarui Data Nilai Mahasiswa',
        description: '### 📋 1. Request Headers
| Header | Tipe Data | Wajib / Required | Keterangan | Contoh Nilai |
| :--- | :--- | :--- | :--- | :--- |
| `Authorization` | `string` | **Ya (Required)** | Token Bearer JWT hasil otentikasi login | `Bearer eyJhbGci...` |
| `Content-Type` | `string` | **Ya (Required)** | Tipe konten payload body permintaan | `application/json` |
| `Accept` | `string` | **Ya (Required)** | Tipe konten respon server yang diharapkan | `application/json` |

### 🔍 2. Path Parameters
| Parameter | Tipe Data | Wajib / Required | Keterangan | Contoh Nilai |
| :--- | :--- | :--- | :--- | :--- |
| `id` | `integer` | **Ya (Required)** | Parameter ID unik id pada path URL | `1` |

### 📥 3. Request Body
| Parameter | Tipe Data | Wajib / Required | Keterangan | Contoh Riil |
| :--- | :--- | :--- | :--- | :--- |
| `enrollment_mahasiswa_angkatan_kelas_id` | `integer` | **Ya (Required)** | ID referensi enrollment mahasiswa pada angkatan kelas | `12` |
| `mata_kuliah_angkatan_kelas_id` | `integer` | **Ya (Required)** | ID referensi penawaran mata kuliah pada angkatan kelas | `93` |
| `nilai_angka` | `number` | **Ya (Required)** | Nilai capaian akademik mahasiswa dalam format angka (0.00 - 100.00) | `94.5` |
| `nilai_huruf` | `string` | **Ya (Required)** | Indeks prestasi huruf mutu (A, AB, B, BC, C, D, E) | `A` |
| `bobot_nilai` | `number` | **Ya (Required)** | Bobot angka mutu kredit akademik (4.00, 3.50, 3.00, 2.50, 2.00, 1.00, 0.00) | `4` |
| `status_kelulusan` | `string` | **Ya (Required)** | Status kelulusan mata kuliah (Lulus / Tidak Lulus) | `Lulus` |

### 📤 4. Struktur Response
| Status HTTP | Field / Properti | Tipe Data | Wajib / Required | Keterangan | Contoh Nilai |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **200 OK** | `success` | `boolean` | **Ya (Required)** | Indikator status keberhasilan (`true`) | `true` |
| | `code` | `integer` | **Ya (Required)** | HTTP Status Code (200) | `200` |
| | `message` | `string` | **Ya (Required)** | Pesan informasi respon server | "Operasi berhasil" |
| | `data` | `object` | **Ya (Required)** | Objek data entitas hasil operasi | `{ ... }` |
| | `data.id` | `integer` | **Ya (Required)** | Id | `104` |
| | `data.enrollment_mahasiswa_angkatan_kelas_id` | `integer` | **Ya (Required)** | ID referensi enrollment mahasiswa pada angkatan kelas | `12` |
| | `data.mata_kuliah_angkatan_kelas_id` | `integer` | **Ya (Required)** | ID referensi penawaran mata kuliah pada angkatan kelas | `93` |
| | `data.nilai_angka` | `number` | **Ya (Required)** | Nilai capaian akademik mahasiswa dalam format angka (0.00 - 100.00) | `94.5` |
| | `data.nilai_huruf` | `string` | **Ya (Required)** | Indeks prestasi huruf mutu (A, AB, B, BC, C, D, E) | `"A"` |
| | `data.bobot_nilai` | `number` | **Ya (Required)** | Bobot angka mutu kredit akademik (4.00, 3.50, 3.00, 2.50, 2.00, 1.00, 0.00) | `4` |
| | `data.status_kelulusan` | `string` | **Ya (Required)** | Status kelulusan mata kuliah (Lulus / Tidak Lulus) | `"Lulus"` |
| | `data.created_at` | `string` | **Ya (Required)** | Created At | `"2026-10-19 03:56:52"` |
| | `data.updated_at` | `string` | **Ya (Required)** | Updated At | `"2026-10-19 03:56:52"` |
| **422 Unprocessable** | `success` | `boolean` | **Ya (Required)** | Indikator kegagalan validasi (`false`) | `false` |
| | `code` | `integer` | **Ya (Required)** | HTTP Status Code (422) | `422` |
| | `message` | `string` | **Ya (Required)** | Ringkasan pesan kegagalan validasi | \\"The given data was invalid.\\" |
| | `errors` | `object` | **Ya (Required)** | Kamus pesan kesalahan validasi per field input | `{\\"field\\": [\\"Pesan error\\"]}` |
| **401 Unauthorized** | `message` | `string` | **Ya (Required)** | Pesan otentikasi gagal saat token kosong atau kedaluwarsa | \\"Unauthenticated.\\" |
| **403 Forbidden** | `message` | `string` | **Ya (Required)** | Pesan penolakan izin akses Role/Permission | \\"User does not have the right permissions.\\" |
| **404 Not Found** | `message` | `string` | **Ya (Required)** | Data referensi dengan ID yang diminta tidak ditemukan di database | \\"Data tidak ditemukan\\" |
| **400 Bad Request** | `message` | `string` | **Ya (Required)** | Permintaan tidak valid atau data berelasi masih terikat | \\"Permintaan tidak valid\\" |',
        tags: ['Nilai Mahasiswa'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID data id',
                schema: new OA\Schema(type: 'integer', example: 1)
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['enrollment_mahasiswa_angkatan_kelas_id', 'mata_kuliah_angkatan_kelas_id', 'nilai_angka', 'nilai_huruf', 'bobot_nilai', 'status_kelulusan'],
                properties: [
                    new OA\Property(property: 'enrollment_mahasiswa_angkatan_kelas_id', type: 'integer', example: 12, description: 'ID referensi enrollment mahasiswa pada angkatan kelas'),
                    new OA\Property(property: 'mata_kuliah_angkatan_kelas_id', type: 'integer', example: 93, description: 'ID referensi penawaran mata kuliah pada angkatan kelas'),
                    new OA\Property(property: 'nilai_angka', type: 'number', example: 94.5, description: 'Nilai capaian akademik mahasiswa dalam format angka (0.00 - 100.00)'),
                    new OA\Property(property: 'nilai_huruf', type: 'string', example: 'A', description: 'Indeks prestasi huruf mutu (A, AB, B, BC, C, D, E)'),
                    new OA\Property(property: 'bobot_nilai', type: 'number', example: 4, description: 'Bobot angka mutu kredit akademik (4.00, 3.50, 3.00, 2.50, 2.00, 1.00, 0.00)'),
                    new OA\Property(property: 'status_kelulusan', type: 'string', example: 'Lulus', description: 'Status kelulusan mata kuliah (Lulus / Tidak Lulus)'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Operasi berhasil'),
            new OA\Response(response: 400, description: 'Permintaan tidak valid / Data terkait masih digunakan'),
            new OA\Response(response: 401, description: 'Unauthenticated / Token tidak valid'),
            new OA\Response(response: 403, description: 'Forbidden / Tidak memiliki hak akses'),
            new OA\Response(response: 404, description: 'Data tidak ditemukan'),
            new OA\Response(response: 422, description: 'Validasi entitas gagal'),
        ]
    )]
    public function update(): void {}

    #[OA\Delete(
        path: '/api/nilai-mahasiswa/{id}',
        summary: 'Hapus Data Nilai Mahasiswa',
        description: '### 📋 1. Request Headers
| Header | Tipe Data | Wajib / Required | Keterangan | Contoh Nilai |
| :--- | :--- | :--- | :--- | :--- |
| `Authorization` | `string` | **Ya (Required)** | Token Bearer JWT hasil otentikasi login | `Bearer eyJhbGci...` |
| `Accept` | `string` | **Ya (Required)** | Tipe konten respon server yang diharapkan | `application/json` |

### 🔍 2. Path Parameters
| Parameter | Tipe Data | Wajib / Required | Keterangan | Contoh Nilai |
| :--- | :--- | :--- | :--- | :--- |
| `id` | `integer` | **Ya (Required)** | Parameter ID unik id pada path URL | `1` |

### 📤 3. Struktur Response
| Status HTTP | Field / Properti | Tipe Data | Wajib / Required | Keterangan | Contoh Nilai |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **200 OK** | `success` | `boolean` | **Ya (Required)** | Indikator status keberhasilan (`true`) | `true` |
| | `code` | `integer` | **Ya (Required)** | HTTP Status Code (200) | `200` |
| | `message` | `string` | **Ya (Required)** | Pesan informasi respon server | "Data berhasil dihapus" |
| | `data` | `null / object` | *Tidak (Opsional)* | Payload hasil operasi hapus | `null` |
| **401 Unauthorized** | `message` | `string` | **Ya (Required)** | Pesan otentikasi gagal saat token kosong atau kedaluwarsa | \\"Unauthenticated.\\" |
| **403 Forbidden** | `message` | `string` | **Ya (Required)** | Pesan penolakan izin akses Role/Permission | \\"User does not have the right permissions.\\" |
| **404 Not Found** | `message` | `string` | **Ya (Required)** | Data referensi dengan ID yang diminta tidak ditemukan di database | \\"Data tidak ditemukan\\" |
| **400 Bad Request** | `message` | `string` | **Ya (Required)** | Permintaan tidak valid atau data berelasi masih terikat | \\"Permintaan tidak valid\\" |',
        tags: ['Nilai Mahasiswa'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID data id',
                schema: new OA\Schema(type: 'integer', example: 1)
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Operasi berhasil'),
            new OA\Response(response: 400, description: 'Permintaan tidak valid / Data terkait masih digunakan'),
            new OA\Response(response: 401, description: 'Unauthenticated / Token tidak valid'),
            new OA\Response(response: 403, description: 'Forbidden / Tidak memiliki hak akses'),
            new OA\Response(response: 404, description: 'Data tidak ditemukan'),
            new OA\Response(response: 422, description: 'Validasi entitas gagal'),
        ]
    )]
    public function destroy(): void {}
}
