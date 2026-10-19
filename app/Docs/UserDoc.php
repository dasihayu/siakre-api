<?php

namespace App\Docs;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Manajemen User', description: 'Endpoint API Manajemen User')]
class UserDoc
{
    #[OA\Get(
        path: '/api/users',
        summary: 'Daftar Semua Data Manajemen User',
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
| | `data[].id` | `integer` | **Ya (Required)** | Id | `8` |
| | `data[].name` | `string` | **Ya (Required)** | Name | `"Direktur Politeknik Negeri Semarang"` |
| | `data[].email` | `string` | **Ya (Required)** | Email | `"pimpinan@polines.ac.id"` |
| | `data[].google_id` | `string` | *Tidak (Opsional / Nullable)* | Google Id | `null` |
| | `data[].avatar` | `string` | *Tidak (Opsional / Nullable)* | Avatar | `null` |
| | `data[].role` | `string` | **Ya (Required)** | Role | `"PIMPINAN"` |
| | `data[].email_verified_at` | `string` | **Ya (Required)** | Email Verified At | `"2026-10-19 03:13:32"` |
| | `data[].created_at` | `string` | **Ya (Required)** | Created At | `"2026-10-19 03:13:32"` |
| | `data[].updated_at` | `string` | **Ya (Required)** | Updated At | `"2026-10-19 03:13:32"` |
| **401 Unauthorized** | `message` | `string` | **Ya (Required)** | Pesan otentikasi gagal saat token kosong atau kedaluwarsa | \\"Unauthenticated.\\" |
| **403 Forbidden** | `message` | `string` | **Ya (Required)** | Pesan penolakan izin akses Role/Permission | \\"User does not have the right permissions.\\" |
| **400 Bad Request** | `message` | `string` | **Ya (Required)** | Permintaan tidak valid atau data berelasi masih terikat | \\"Permintaan tidak valid\\" |',
        tags: ['Manajemen User'],
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
        path: '/api/users',
        summary: 'Tambah Data Manajemen User Baru',
        description: '### 📋 1. Request Headers
| Header | Tipe Data | Wajib / Required | Keterangan | Contoh Nilai |
| :--- | :--- | :--- | :--- | :--- |
| `Authorization` | `string` | **Ya (Required)** | Token Bearer JWT hasil otentikasi login | `Bearer eyJhbGci...` |
| `Content-Type` | `string` | **Ya (Required)** | Tipe konten payload body permintaan | `application/json` |
| `Accept` | `string` | **Ya (Required)** | Tipe konten respon server yang diharapkan | `application/json` |

### 📥 2. Request Body
| Parameter | Tipe Data | Wajib / Required | Keterangan | Contoh Riil |
| :--- | :--- | :--- | :--- | :--- |
| `nama` | `string` | **Ya (Required)** | Nama lengkap pengguna | `Dr. Andi Wijaya, S.T., M.Kom.` |
| `email` | `string (email)` | **Ya (Required)** | Email resmi institusi Polines (@polines.ac.id) | `andi@polines.ac.id` |
| `password` | `string (password)` | **Ya (Required)** | Kata sandi pengguna (min 8 karakter) | `password123` |
| `role` | `string` | **Ya (Required)** | Role pengguna (ADMINISTRATOR, OPERATOR, KAPRODI, DOSEN, REVIEWER, PIMPINAN) | `DOSEN` |

### 📤 3. Struktur Response
| Status HTTP | Field / Properti | Tipe Data | Wajib / Required | Keterangan | Contoh Nilai |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **201 Created** | `success` | `boolean` | **Ya (Required)** | Indikator status keberhasilan (`true`) | `true` |
| | `code` | `integer` | **Ya (Required)** | HTTP Status Code (201) | `201` |
| | `message` | `string` | **Ya (Required)** | Pesan informasi respon server | "Data berhasil dibuat" |
| | `data` | `object` | **Ya (Required)** | Objek data entitas hasil operasi | `{ ... }` |
| | `data.id` | `integer` | **Ya (Required)** | Id | `8` |
| | `data.name` | `string` | **Ya (Required)** | Name | `"Direktur Politeknik Negeri Semarang"` |
| | `data.email` | `string` | **Ya (Required)** | Email resmi institusi Polines (@polines.ac.id) | `"pimpinan@polines.ac.id"` |
| | `data.google_id` | `string` | *Tidak (Opsional / Nullable)* | Google Id | `null` |
| | `data.avatar` | `string` | *Tidak (Opsional / Nullable)* | Avatar | `null` |
| | `data.role` | `string` | **Ya (Required)** | Role pengguna (ADMINISTRATOR, OPERATOR, KAPRODI, DOSEN, REVIEWER, PIMPINAN) | `"PIMPINAN"` |
| | `data.email_verified_at` | `string` | **Ya (Required)** | Email Verified At | `"2026-10-19 03:13:32"` |
| | `data.created_at` | `string` | **Ya (Required)** | Created At | `"2026-10-19 03:13:32"` |
| | `data.updated_at` | `string` | **Ya (Required)** | Updated At | `"2026-10-19 03:13:32"` |
| **422 Unprocessable** | `success` | `boolean` | **Ya (Required)** | Indikator kegagalan validasi (`false`) | `false` |
| | `code` | `integer` | **Ya (Required)** | HTTP Status Code (422) | `422` |
| | `message` | `string` | **Ya (Required)** | Ringkasan pesan kegagalan validasi | \\"The given data was invalid.\\" |
| | `errors` | `object` | **Ya (Required)** | Kamus pesan kesalahan validasi per field input | `{\\"field\\": [\\"Pesan error\\"]}` |
| **401 Unauthorized** | `message` | `string` | **Ya (Required)** | Pesan otentikasi gagal saat token kosong atau kedaluwarsa | \\"Unauthenticated.\\" |
| **403 Forbidden** | `message` | `string` | **Ya (Required)** | Pesan penolakan izin akses Role/Permission | \\"User does not have the right permissions.\\" |
| **400 Bad Request** | `message` | `string` | **Ya (Required)** | Permintaan tidak valid atau data berelasi masih terikat | \\"Permintaan tidak valid\\" |',
        tags: ['Manajemen User'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['nama', 'email', 'password', 'role'],
                properties: [
                    new OA\Property(property: 'nama', type: 'string', example: 'Dr. Andi Wijaya, S.T., M.Kom.', description: 'Nama lengkap pengguna'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'andi@polines.ac.id', description: 'Email resmi institusi Polines (@polines.ac.id)'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'password123', description: 'Kata sandi pengguna (min 8 karakter)'),
                    new OA\Property(property: 'role', type: 'string', example: 'DOSEN', description: 'Role pengguna (ADMINISTRATOR, OPERATOR, KAPRODI, DOSEN, REVIEWER, PIMPINAN)'),
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
        path: '/api/users/{user}',
        summary: 'Detail Data Manajemen User',
        description: '### 📋 1. Request Headers
| Header | Tipe Data | Wajib / Required | Keterangan | Contoh Nilai |
| :--- | :--- | :--- | :--- | :--- |
| `Authorization` | `string` | **Ya (Required)** | Token Bearer JWT hasil otentikasi login | `Bearer eyJhbGci...` |
| `Accept` | `string` | **Ya (Required)** | Tipe konten respon server yang diharapkan | `application/json` |

### 🔍 2. Path Parameters
| Parameter | Tipe Data | Wajib / Required | Keterangan | Contoh Nilai |
| :--- | :--- | :--- | :--- | :--- |
| `user` | `integer` | **Ya (Required)** | Parameter ID unik user pada path URL | `1` |

### 📤 3. Struktur Response
| Status HTTP | Field / Properti | Tipe Data | Wajib / Required | Keterangan | Contoh Nilai |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **200 OK** | `success` | `boolean` | **Ya (Required)** | Indikator status keberhasilan (`true`) | `true` |
| | `code` | `integer` | **Ya (Required)** | HTTP Status Code (200) | `200` |
| | `message` | `string` | **Ya (Required)** | Pesan informasi respon server | "Operasi berhasil" |
| | `data` | `object` | **Ya (Required)** | Objek data entitas hasil operasi | `{ ... }` |
| | `data.id` | `integer` | **Ya (Required)** | Id | `8` |
| | `data.name` | `string` | **Ya (Required)** | Name | `"Direktur Politeknik Negeri Semarang"` |
| | `data.email` | `string` | **Ya (Required)** | Email | `"pimpinan@polines.ac.id"` |
| | `data.google_id` | `string` | *Tidak (Opsional / Nullable)* | Google Id | `null` |
| | `data.avatar` | `string` | *Tidak (Opsional / Nullable)* | Avatar | `null` |
| | `data.role` | `string` | **Ya (Required)** | Role | `"PIMPINAN"` |
| | `data.email_verified_at` | `string` | **Ya (Required)** | Email Verified At | `"2026-10-19 03:13:32"` |
| | `data.created_at` | `string` | **Ya (Required)** | Created At | `"2026-10-19 03:13:32"` |
| | `data.updated_at` | `string` | **Ya (Required)** | Updated At | `"2026-10-19 03:13:32"` |
| **401 Unauthorized** | `message` | `string` | **Ya (Required)** | Pesan otentikasi gagal saat token kosong atau kedaluwarsa | \\"Unauthenticated.\\" |
| **403 Forbidden** | `message` | `string` | **Ya (Required)** | Pesan penolakan izin akses Role/Permission | \\"User does not have the right permissions.\\" |
| **404 Not Found** | `message` | `string` | **Ya (Required)** | Data referensi dengan ID yang diminta tidak ditemukan di database | \\"Data tidak ditemukan\\" |
| **400 Bad Request** | `message` | `string` | **Ya (Required)** | Permintaan tidak valid atau data berelasi masih terikat | \\"Permintaan tidak valid\\" |',
        tags: ['Manajemen User'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'user',
                in: 'path',
                required: true,
                description: 'ID data user',
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
        path: '/api/users/{user}',
        summary: 'Perbarui Data Manajemen User',
        description: '### 📋 1. Request Headers
| Header | Tipe Data | Wajib / Required | Keterangan | Contoh Nilai |
| :--- | :--- | :--- | :--- | :--- |
| `Authorization` | `string` | **Ya (Required)** | Token Bearer JWT hasil otentikasi login | `Bearer eyJhbGci...` |
| `Content-Type` | `string` | **Ya (Required)** | Tipe konten payload body permintaan | `application/json` |
| `Accept` | `string` | **Ya (Required)** | Tipe konten respon server yang diharapkan | `application/json` |

### 🔍 2. Path Parameters
| Parameter | Tipe Data | Wajib / Required | Keterangan | Contoh Nilai |
| :--- | :--- | :--- | :--- | :--- |
| `user` | `integer` | **Ya (Required)** | Parameter ID unik user pada path URL | `1` |

### 📥 3. Request Body
| Parameter | Tipe Data | Wajib / Required | Keterangan | Contoh Riil |
| :--- | :--- | :--- | :--- | :--- |
| `nama` | `string` | **Ya (Required)** | Nama lengkap pengguna | `Dr. Andi Wijaya, S.T., M.Kom.` |
| `email` | `string (email)` | **Ya (Required)** | Email resmi institusi Polines (@polines.ac.id) | `andi@polines.ac.id` |
| `password` | `string (password)` | **Ya (Required)** | Kata sandi pengguna (min 8 karakter) | `password123` |
| `role` | `string` | **Ya (Required)** | Role pengguna (ADMINISTRATOR, OPERATOR, KAPRODI, DOSEN, REVIEWER, PIMPINAN) | `DOSEN` |

### 📤 4. Struktur Response
| Status HTTP | Field / Properti | Tipe Data | Wajib / Required | Keterangan | Contoh Nilai |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **200 OK** | `success` | `boolean` | **Ya (Required)** | Indikator status keberhasilan (`true`) | `true` |
| | `code` | `integer` | **Ya (Required)** | HTTP Status Code (200) | `200` |
| | `message` | `string` | **Ya (Required)** | Pesan informasi respon server | "Operasi berhasil" |
| | `data` | `object` | **Ya (Required)** | Objek data entitas hasil operasi | `{ ... }` |
| | `data.id` | `integer` | **Ya (Required)** | Id | `8` |
| | `data.name` | `string` | **Ya (Required)** | Name | `"Direktur Politeknik Negeri Semarang"` |
| | `data.email` | `string` | **Ya (Required)** | Email resmi institusi Polines (@polines.ac.id) | `"pimpinan@polines.ac.id"` |
| | `data.google_id` | `string` | *Tidak (Opsional / Nullable)* | Google Id | `null` |
| | `data.avatar` | `string` | *Tidak (Opsional / Nullable)* | Avatar | `null` |
| | `data.role` | `string` | **Ya (Required)** | Role pengguna (ADMINISTRATOR, OPERATOR, KAPRODI, DOSEN, REVIEWER, PIMPINAN) | `"PIMPINAN"` |
| | `data.email_verified_at` | `string` | **Ya (Required)** | Email Verified At | `"2026-10-19 03:13:32"` |
| | `data.created_at` | `string` | **Ya (Required)** | Created At | `"2026-10-19 03:13:32"` |
| | `data.updated_at` | `string` | **Ya (Required)** | Updated At | `"2026-10-19 03:13:32"` |
| **422 Unprocessable** | `success` | `boolean` | **Ya (Required)** | Indikator kegagalan validasi (`false`) | `false` |
| | `code` | `integer` | **Ya (Required)** | HTTP Status Code (422) | `422` |
| | `message` | `string` | **Ya (Required)** | Ringkasan pesan kegagalan validasi | \\"The given data was invalid.\\" |
| | `errors` | `object` | **Ya (Required)** | Kamus pesan kesalahan validasi per field input | `{\\"field\\": [\\"Pesan error\\"]}` |
| **401 Unauthorized** | `message` | `string` | **Ya (Required)** | Pesan otentikasi gagal saat token kosong atau kedaluwarsa | \\"Unauthenticated.\\" |
| **403 Forbidden** | `message` | `string` | **Ya (Required)** | Pesan penolakan izin akses Role/Permission | \\"User does not have the right permissions.\\" |
| **404 Not Found** | `message` | `string` | **Ya (Required)** | Data referensi dengan ID yang diminta tidak ditemukan di database | \\"Data tidak ditemukan\\" |
| **400 Bad Request** | `message` | `string` | **Ya (Required)** | Permintaan tidak valid atau data berelasi masih terikat | \\"Permintaan tidak valid\\" |',
        tags: ['Manajemen User'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'user',
                in: 'path',
                required: true,
                description: 'ID data user',
                schema: new OA\Schema(type: 'integer', example: 1)
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['nama', 'email', 'password', 'role'],
                properties: [
                    new OA\Property(property: 'nama', type: 'string', example: 'Dr. Andi Wijaya, S.T., M.Kom.', description: 'Nama lengkap pengguna'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'andi@polines.ac.id', description: 'Email resmi institusi Polines (@polines.ac.id)'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'password123', description: 'Kata sandi pengguna (min 8 karakter)'),
                    new OA\Property(property: 'role', type: 'string', example: 'DOSEN', description: 'Role pengguna (ADMINISTRATOR, OPERATOR, KAPRODI, DOSEN, REVIEWER, PIMPINAN)'),
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
        path: '/api/users/{user}',
        summary: 'Hapus Data Manajemen User',
        description: '### 📋 1. Request Headers
| Header | Tipe Data | Wajib / Required | Keterangan | Contoh Nilai |
| :--- | :--- | :--- | :--- | :--- |
| `Authorization` | `string` | **Ya (Required)** | Token Bearer JWT hasil otentikasi login | `Bearer eyJhbGci...` |
| `Accept` | `string` | **Ya (Required)** | Tipe konten respon server yang diharapkan | `application/json` |

### 🔍 2. Path Parameters
| Parameter | Tipe Data | Wajib / Required | Keterangan | Contoh Nilai |
| :--- | :--- | :--- | :--- | :--- |
| `user` | `integer` | **Ya (Required)** | Parameter ID unik user pada path URL | `1` |

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
        tags: ['Manajemen User'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'user',
                in: 'path',
                required: true,
                description: 'ID data user',
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
