<?php

namespace App\Docs;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Tahun Akademik', description: 'Endpoint API Tahun Akademik')]
class TahunAkademikDoc
{
    #[OA\Get(
        path: '/api/tahun-akademik',
        summary: 'Daftar Semua Data Tahun Akademik',
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
| | `data[].id` | `integer` | **Ya (Required)** | Id | `6` |
| | `data[].tahun` | `integer` | **Ya (Required)** | Tahun | `2026` |
| | `data[].created_at` | `string` | **Ya (Required)** | Created At | `"2026-10-19 03:13:34"` |
| | `data[].updated_at` | `string` | **Ya (Required)** | Updated At | `"2026-10-19 03:13:34"` |
| **401 Unauthorized** | `message` | `string` | **Ya (Required)** | Pesan otentikasi gagal saat token kosong atau kedaluwarsa | \\"Unauthenticated.\\" |
| **403 Forbidden** | `message` | `string` | **Ya (Required)** | Pesan penolakan izin akses Role/Permission | \\"User does not have the right permissions.\\" |
| **400 Bad Request** | `message` | `string` | **Ya (Required)** | Permintaan tidak valid atau data berelasi masih terikat | \\"Permintaan tidak valid\\" |',
        tags: ['Tahun Akademik'],
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
        path: '/api/tahun-akademik',
        summary: 'Tambah Data Tahun Akademik Baru',
        description: '### 📋 1. Request Headers
| Header | Tipe Data | Wajib / Required | Keterangan | Contoh Nilai |
| :--- | :--- | :--- | :--- | :--- |
| `Authorization` | `string` | **Ya (Required)** | Token Bearer JWT hasil otentikasi login | `Bearer eyJhbGci...` |
| `Content-Type` | `string` | **Ya (Required)** | Tipe konten payload body permintaan | `application/json` |
| `Accept` | `string` | **Ya (Required)** | Tipe konten respon server yang diharapkan | `application/json` |

### 📥 2. Request Body
| Parameter | Tipe Data | Wajib / Required | Keterangan | Contoh Riil |
| :--- | :--- | :--- | :--- | :--- |
| `tahun` | `integer` | **Ya (Required)** | Tahun kalender akademik | `2026` |
| `semester` | `string` | **Ya (Required)** | Semester | `Contoh Semester` |
| `is_active` | `string` | **Ya (Required)** | Is Active | `Contoh Is Active` |

### 📤 3. Struktur Response
| Status HTTP | Field / Properti | Tipe Data | Wajib / Required | Keterangan | Contoh Nilai |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **201 Created** | `success` | `boolean` | **Ya (Required)** | Indikator status keberhasilan (`true`) | `true` |
| | `code` | `integer` | **Ya (Required)** | HTTP Status Code (201) | `201` |
| | `message` | `string` | **Ya (Required)** | Pesan informasi respon server | "Data berhasil dibuat" |
| | `data` | `object` | **Ya (Required)** | Objek data entitas hasil operasi | `{ ... }` |
| | `data.id` | `integer` | **Ya (Required)** | Id | `6` |
| | `data.tahun` | `integer` | **Ya (Required)** | Tahun kalender akademik | `2026` |
| | `data.created_at` | `string` | **Ya (Required)** | Created At | `"2026-10-19 03:13:34"` |
| | `data.updated_at` | `string` | **Ya (Required)** | Updated At | `"2026-10-19 03:13:34"` |
| **422 Unprocessable** | `success` | `boolean` | **Ya (Required)** | Indikator kegagalan validasi (`false`) | `false` |
| | `code` | `integer` | **Ya (Required)** | HTTP Status Code (422) | `422` |
| | `message` | `string` | **Ya (Required)** | Ringkasan pesan kegagalan validasi | \\"The given data was invalid.\\" |
| | `errors` | `object` | **Ya (Required)** | Kamus pesan kesalahan validasi per field input | `{\\"field\\": [\\"Pesan error\\"]}` |
| **401 Unauthorized** | `message` | `string` | **Ya (Required)** | Pesan otentikasi gagal saat token kosong atau kedaluwarsa | \\"Unauthenticated.\\" |
| **403 Forbidden** | `message` | `string` | **Ya (Required)** | Pesan penolakan izin akses Role/Permission | \\"User does not have the right permissions.\\" |
| **400 Bad Request** | `message` | `string` | **Ya (Required)** | Permintaan tidak valid atau data berelasi masih terikat | \\"Permintaan tidak valid\\" |',
        tags: ['Tahun Akademik'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['tahun', 'semester', 'is_active'],
                properties: [
                    new OA\Property(property: 'tahun', type: 'integer', example: 2026, description: 'Tahun kalender akademik'),
                    new OA\Property(property: 'semester', type: 'string', example: 'Contoh Semester', description: 'Semester'),
                    new OA\Property(property: 'is_active', type: 'string', example: 'Contoh Is Active', description: 'Is Active'),
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
        path: '/api/tahun-akademik/{id}',
        summary: 'Detail Data Tahun Akademik',
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
| | `data.id` | `integer` | **Ya (Required)** | Id | `6` |
| | `data.tahun` | `integer` | **Ya (Required)** | Tahun | `2026` |
| | `data.created_at` | `string` | **Ya (Required)** | Created At | `"2026-10-19 03:13:34"` |
| | `data.updated_at` | `string` | **Ya (Required)** | Updated At | `"2026-10-19 03:13:34"` |
| **401 Unauthorized** | `message` | `string` | **Ya (Required)** | Pesan otentikasi gagal saat token kosong atau kedaluwarsa | \\"Unauthenticated.\\" |
| **403 Forbidden** | `message` | `string` | **Ya (Required)** | Pesan penolakan izin akses Role/Permission | \\"User does not have the right permissions.\\" |
| **404 Not Found** | `message` | `string` | **Ya (Required)** | Data referensi dengan ID yang diminta tidak ditemukan di database | \\"Data tidak ditemukan\\" |
| **400 Bad Request** | `message` | `string` | **Ya (Required)** | Permintaan tidak valid atau data berelasi masih terikat | \\"Permintaan tidak valid\\" |',
        tags: ['Tahun Akademik'],
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
        path: '/api/tahun-akademik/{id}',
        summary: 'Perbarui Data Tahun Akademik',
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
| `tahun` | `integer` | **Ya (Required)** | Tahun kalender akademik | `2026` |
| `semester` | `string` | **Ya (Required)** | Semester | `Contoh Semester` |
| `is_active` | `string` | **Ya (Required)** | Is Active | `Contoh Is Active` |

### 📤 4. Struktur Response
| Status HTTP | Field / Properti | Tipe Data | Wajib / Required | Keterangan | Contoh Nilai |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **200 OK** | `success` | `boolean` | **Ya (Required)** | Indikator status keberhasilan (`true`) | `true` |
| | `code` | `integer` | **Ya (Required)** | HTTP Status Code (200) | `200` |
| | `message` | `string` | **Ya (Required)** | Pesan informasi respon server | "Operasi berhasil" |
| | `data` | `object` | **Ya (Required)** | Objek data entitas hasil operasi | `{ ... }` |
| | `data.id` | `integer` | **Ya (Required)** | Id | `6` |
| | `data.tahun` | `integer` | **Ya (Required)** | Tahun kalender akademik | `2026` |
| | `data.created_at` | `string` | **Ya (Required)** | Created At | `"2026-10-19 03:13:34"` |
| | `data.updated_at` | `string` | **Ya (Required)** | Updated At | `"2026-10-19 03:13:34"` |
| **422 Unprocessable** | `success` | `boolean` | **Ya (Required)** | Indikator kegagalan validasi (`false`) | `false` |
| | `code` | `integer` | **Ya (Required)** | HTTP Status Code (422) | `422` |
| | `message` | `string` | **Ya (Required)** | Ringkasan pesan kegagalan validasi | \\"The given data was invalid.\\" |
| | `errors` | `object` | **Ya (Required)** | Kamus pesan kesalahan validasi per field input | `{\\"field\\": [\\"Pesan error\\"]}` |
| **401 Unauthorized** | `message` | `string` | **Ya (Required)** | Pesan otentikasi gagal saat token kosong atau kedaluwarsa | \\"Unauthenticated.\\" |
| **403 Forbidden** | `message` | `string` | **Ya (Required)** | Pesan penolakan izin akses Role/Permission | \\"User does not have the right permissions.\\" |
| **404 Not Found** | `message` | `string` | **Ya (Required)** | Data referensi dengan ID yang diminta tidak ditemukan di database | \\"Data tidak ditemukan\\" |
| **400 Bad Request** | `message` | `string` | **Ya (Required)** | Permintaan tidak valid atau data berelasi masih terikat | \\"Permintaan tidak valid\\" |',
        tags: ['Tahun Akademik'],
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
                required: ['tahun', 'semester', 'is_active'],
                properties: [
                    new OA\Property(property: 'tahun', type: 'integer', example: 2026, description: 'Tahun kalender akademik'),
                    new OA\Property(property: 'semester', type: 'string', example: 'Contoh Semester', description: 'Semester'),
                    new OA\Property(property: 'is_active', type: 'string', example: 'Contoh Is Active', description: 'Is Active'),
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
        path: '/api/tahun-akademik/{id}',
        summary: 'Hapus Data Tahun Akademik',
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
        tags: ['Tahun Akademik'],
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
