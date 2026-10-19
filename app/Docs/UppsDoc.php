<?php

namespace App\Docs;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'UPPS', description: 'Endpoint API UPPS')]
class UppsDoc
{
    #[OA\Get(
        path: '/api/upps',
        summary: 'Daftar Semua Data UPPS',
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
| | `data[].id` | `integer` | **Ya (Required)** | Id | `3` |
| | `data[].id_pt` | `integer` | **Ya (Required)** | Id Pt | `1` |
| | `data[].kode_upps` | `string` | **Ya (Required)** | Kode Upps | `"UPPS03"` |
| | `data[].nama_upps` | `string` | **Ya (Required)** | Nama Upps | `"Jurusan Teknik Sipil"` |
| | `data[].pimpinan_upps` | `string` | **Ya (Required)** | Pimpinan Upps | `"Dwi Yanuar, S.T., M.T."` |
| | `data[].prodi_id` | `integer` | **Ya (Required)** | Prodi Id | `1` |
| | `data[].jenis` | `string` | **Ya (Required)** | Jenis | `"Jurusan"` |
| | `data[].created_at` | `string` | **Ya (Required)** | Created At | `"2026-10-19 03:13:33"` |
| | `data[].updated_at` | `string` | **Ya (Required)** | Updated At | `"2026-10-19 03:13:33"` |
| **401 Unauthorized** | `message` | `string` | **Ya (Required)** | Pesan otentikasi gagal saat token kosong atau kedaluwarsa | \\"Unauthenticated.\\" |
| **403 Forbidden** | `message` | `string` | **Ya (Required)** | Pesan penolakan izin akses Role/Permission | \\"User does not have the right permissions.\\" |
| **400 Bad Request** | `message` | `string` | **Ya (Required)** | Permintaan tidak valid atau data berelasi masih terikat | \\"Permintaan tidak valid\\" |',
        tags: ['UPPS'],
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
        path: '/api/upps',
        summary: 'Tambah Data UPPS Baru',
        description: '### 📋 1. Request Headers
| Header | Tipe Data | Wajib / Required | Keterangan | Contoh Nilai |
| :--- | :--- | :--- | :--- | :--- |
| `Authorization` | `string` | **Ya (Required)** | Token Bearer JWT hasil otentikasi login | `Bearer eyJhbGci...` |
| `Content-Type` | `string` | **Ya (Required)** | Tipe konten payload body permintaan | `application/json` |
| `Accept` | `string` | **Ya (Required)** | Tipe konten respon server yang diharapkan | `application/json` |

### 📥 2. Request Body
| Parameter | Tipe Data | Wajib / Required | Keterangan | Contoh Riil |
| :--- | :--- | :--- | :--- | :--- |
| `id_pt` | `integer` | **Ya (Required)** | ID referensi Perguruan Tinggi | `1` |
| `kode_upps` | `string` | **Ya (Required)** | Kode Unit Pengelola Program Studi (Fakultas/Jurusan) | `UPPS03` |
| `nama_upps` | `string` | **Ya (Required)** | Nama resmi Unit Pengelola Program Studi | `Jurusan Teknik Sipil` |
| `pimpinan_upps` | `string` | *Tidak (Opsional / Nullable)* | Nama pimpinan UPPS (Ketua Jurusan / Dekan) | `Dwi Yanuar, S.T., M.T.` |
| `prodi_id` | `integer` | **Ya (Required)** | ID referensi Program Studi | `1` |
| `jenis` | `string` | **Ya (Required)** | Jenis entitas unit pengelola (misal: Jurusan, Fakultas) | `Jurusan` |

### 📤 3. Struktur Response
| Status HTTP | Field / Properti | Tipe Data | Wajib / Required | Keterangan | Contoh Nilai |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **201 Created** | `success` | `boolean` | **Ya (Required)** | Indikator status keberhasilan (`true`) | `true` |
| | `code` | `integer` | **Ya (Required)** | HTTP Status Code (201) | `201` |
| | `message` | `string` | **Ya (Required)** | Pesan informasi respon server | "Data berhasil dibuat" |
| | `data` | `object` | **Ya (Required)** | Objek data entitas hasil operasi | `{ ... }` |
| | `data.id` | `integer` | **Ya (Required)** | Id | `3` |
| | `data.id_pt` | `integer` | **Ya (Required)** | ID referensi Perguruan Tinggi | `1` |
| | `data.kode_upps` | `string` | **Ya (Required)** | Kode Unit Pengelola Program Studi (Fakultas/Jurusan) | `"UPPS03"` |
| | `data.nama_upps` | `string` | **Ya (Required)** | Nama resmi Unit Pengelola Program Studi | `"Jurusan Teknik Sipil"` |
| | `data.pimpinan_upps` | `string` | *Tidak (Opsional / Nullable)* | Nama pimpinan UPPS (Ketua Jurusan / Dekan) | `"Dwi Yanuar, S.T., M.T."` |
| | `data.prodi_id` | `integer` | **Ya (Required)** | ID referensi Program Studi | `1` |
| | `data.jenis` | `string` | **Ya (Required)** | Jenis entitas unit pengelola (misal: Jurusan, Fakultas) | `"Jurusan"` |
| | `data.created_at` | `string` | **Ya (Required)** | Created At | `"2026-10-19 03:13:33"` |
| | `data.updated_at` | `string` | **Ya (Required)** | Updated At | `"2026-10-19 03:13:33"` |
| **422 Unprocessable** | `success` | `boolean` | **Ya (Required)** | Indikator kegagalan validasi (`false`) | `false` |
| | `code` | `integer` | **Ya (Required)** | HTTP Status Code (422) | `422` |
| | `message` | `string` | **Ya (Required)** | Ringkasan pesan kegagalan validasi | \\"The given data was invalid.\\" |
| | `errors` | `object` | **Ya (Required)** | Kamus pesan kesalahan validasi per field input | `{\\"field\\": [\\"Pesan error\\"]}` |
| **401 Unauthorized** | `message` | `string` | **Ya (Required)** | Pesan otentikasi gagal saat token kosong atau kedaluwarsa | \\"Unauthenticated.\\" |
| **403 Forbidden** | `message` | `string` | **Ya (Required)** | Pesan penolakan izin akses Role/Permission | \\"User does not have the right permissions.\\" |
| **400 Bad Request** | `message` | `string` | **Ya (Required)** | Permintaan tidak valid atau data berelasi masih terikat | \\"Permintaan tidak valid\\" |',
        tags: ['UPPS'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['id_pt', 'kode_upps', 'nama_upps', 'prodi_id', 'jenis'],
                properties: [
                    new OA\Property(property: 'id_pt', type: 'integer', example: 1, description: 'ID referensi Perguruan Tinggi'),
                    new OA\Property(property: 'kode_upps', type: 'string', example: 'UPPS03', description: 'Kode Unit Pengelola Program Studi (Fakultas/Jurusan)'),
                    new OA\Property(property: 'nama_upps', type: 'string', example: 'Jurusan Teknik Sipil', description: 'Nama resmi Unit Pengelola Program Studi'),
                    new OA\Property(property: 'pimpinan_upps', type: 'string', example: 'Dwi Yanuar, S.T., M.T.', description: 'Nama pimpinan UPPS (Ketua Jurusan / Dekan)', nullable: true),
                    new OA\Property(property: 'prodi_id', type: 'integer', example: 1, description: 'ID referensi Program Studi'),
                    new OA\Property(property: 'jenis', type: 'string', example: 'Jurusan', description: 'Jenis entitas unit pengelola (misal: Jurusan, Fakultas)'),
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
        path: '/api/upps/{id}',
        summary: 'Detail Data UPPS',
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
| | `data.id` | `integer` | **Ya (Required)** | Id | `3` |
| | `data.id_pt` | `integer` | **Ya (Required)** | Id Pt | `1` |
| | `data.kode_upps` | `string` | **Ya (Required)** | Kode Upps | `"UPPS03"` |
| | `data.nama_upps` | `string` | **Ya (Required)** | Nama Upps | `"Jurusan Teknik Sipil"` |
| | `data.pimpinan_upps` | `string` | **Ya (Required)** | Pimpinan Upps | `"Dwi Yanuar, S.T., M.T."` |
| | `data.prodi_id` | `integer` | **Ya (Required)** | Prodi Id | `1` |
| | `data.jenis` | `string` | **Ya (Required)** | Jenis | `"Jurusan"` |
| | `data.created_at` | `string` | **Ya (Required)** | Created At | `"2026-10-19 03:13:33"` |
| | `data.updated_at` | `string` | **Ya (Required)** | Updated At | `"2026-10-19 03:13:33"` |
| **401 Unauthorized** | `message` | `string` | **Ya (Required)** | Pesan otentikasi gagal saat token kosong atau kedaluwarsa | \\"Unauthenticated.\\" |
| **403 Forbidden** | `message` | `string` | **Ya (Required)** | Pesan penolakan izin akses Role/Permission | \\"User does not have the right permissions.\\" |
| **404 Not Found** | `message` | `string` | **Ya (Required)** | Data referensi dengan ID yang diminta tidak ditemukan di database | \\"Data tidak ditemukan\\" |
| **400 Bad Request** | `message` | `string` | **Ya (Required)** | Permintaan tidak valid atau data berelasi masih terikat | \\"Permintaan tidak valid\\" |',
        tags: ['UPPS'],
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
        path: '/api/upps/{id}',
        summary: 'Perbarui Data UPPS',
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
| `id_pt` | `integer` | **Ya (Required)** | ID referensi Perguruan Tinggi | `1` |
| `kode_upps` | `string` | **Ya (Required)** | Kode Unit Pengelola Program Studi (Fakultas/Jurusan) | `UPPS03` |
| `nama_upps` | `string` | **Ya (Required)** | Nama resmi Unit Pengelola Program Studi | `Jurusan Teknik Sipil` |
| `pimpinan_upps` | `string` | *Tidak (Opsional / Nullable)* | Nama pimpinan UPPS (Ketua Jurusan / Dekan) | `Dwi Yanuar, S.T., M.T.` |
| `prodi_id` | `integer` | **Ya (Required)** | ID referensi Program Studi | `1` |
| `jenis` | `string` | **Ya (Required)** | Jenis entitas unit pengelola (misal: Jurusan, Fakultas) | `Jurusan` |

### 📤 4. Struktur Response
| Status HTTP | Field / Properti | Tipe Data | Wajib / Required | Keterangan | Contoh Nilai |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **200 OK** | `success` | `boolean` | **Ya (Required)** | Indikator status keberhasilan (`true`) | `true` |
| | `code` | `integer` | **Ya (Required)** | HTTP Status Code (200) | `200` |
| | `message` | `string` | **Ya (Required)** | Pesan informasi respon server | "Operasi berhasil" |
| | `data` | `object` | **Ya (Required)** | Objek data entitas hasil operasi | `{ ... }` |
| | `data.id` | `integer` | **Ya (Required)** | Id | `3` |
| | `data.id_pt` | `integer` | **Ya (Required)** | ID referensi Perguruan Tinggi | `1` |
| | `data.kode_upps` | `string` | **Ya (Required)** | Kode Unit Pengelola Program Studi (Fakultas/Jurusan) | `"UPPS03"` |
| | `data.nama_upps` | `string` | **Ya (Required)** | Nama resmi Unit Pengelola Program Studi | `"Jurusan Teknik Sipil"` |
| | `data.pimpinan_upps` | `string` | *Tidak (Opsional / Nullable)* | Nama pimpinan UPPS (Ketua Jurusan / Dekan) | `"Dwi Yanuar, S.T., M.T."` |
| | `data.prodi_id` | `integer` | **Ya (Required)** | ID referensi Program Studi | `1` |
| | `data.jenis` | `string` | **Ya (Required)** | Jenis entitas unit pengelola (misal: Jurusan, Fakultas) | `"Jurusan"` |
| | `data.created_at` | `string` | **Ya (Required)** | Created At | `"2026-10-19 03:13:33"` |
| | `data.updated_at` | `string` | **Ya (Required)** | Updated At | `"2026-10-19 03:13:33"` |
| **422 Unprocessable** | `success` | `boolean` | **Ya (Required)** | Indikator kegagalan validasi (`false`) | `false` |
| | `code` | `integer` | **Ya (Required)** | HTTP Status Code (422) | `422` |
| | `message` | `string` | **Ya (Required)** | Ringkasan pesan kegagalan validasi | \\"The given data was invalid.\\" |
| | `errors` | `object` | **Ya (Required)** | Kamus pesan kesalahan validasi per field input | `{\\"field\\": [\\"Pesan error\\"]}` |
| **401 Unauthorized** | `message` | `string` | **Ya (Required)** | Pesan otentikasi gagal saat token kosong atau kedaluwarsa | \\"Unauthenticated.\\" |
| **403 Forbidden** | `message` | `string` | **Ya (Required)** | Pesan penolakan izin akses Role/Permission | \\"User does not have the right permissions.\\" |
| **404 Not Found** | `message` | `string` | **Ya (Required)** | Data referensi dengan ID yang diminta tidak ditemukan di database | \\"Data tidak ditemukan\\" |
| **400 Bad Request** | `message` | `string` | **Ya (Required)** | Permintaan tidak valid atau data berelasi masih terikat | \\"Permintaan tidak valid\\" |',
        tags: ['UPPS'],
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
                required: ['id_pt', 'kode_upps', 'nama_upps', 'prodi_id', 'jenis'],
                properties: [
                    new OA\Property(property: 'id_pt', type: 'integer', example: 1, description: 'ID referensi Perguruan Tinggi'),
                    new OA\Property(property: 'kode_upps', type: 'string', example: 'UPPS03', description: 'Kode Unit Pengelola Program Studi (Fakultas/Jurusan)'),
                    new OA\Property(property: 'nama_upps', type: 'string', example: 'Jurusan Teknik Sipil', description: 'Nama resmi Unit Pengelola Program Studi'),
                    new OA\Property(property: 'pimpinan_upps', type: 'string', example: 'Dwi Yanuar, S.T., M.T.', description: 'Nama pimpinan UPPS (Ketua Jurusan / Dekan)', nullable: true),
                    new OA\Property(property: 'prodi_id', type: 'integer', example: 1, description: 'ID referensi Program Studi'),
                    new OA\Property(property: 'jenis', type: 'string', example: 'Jurusan', description: 'Jenis entitas unit pengelola (misal: Jurusan, Fakultas)'),
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
        path: '/api/upps/{id}',
        summary: 'Hapus Data UPPS',
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
        tags: ['UPPS'],
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
