<?php

namespace App\Docs;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Mahasiswa', description: 'Endpoint API Mahasiswa')]
class MahasiswaDoc
{
    #[OA\Get(
        path: '/api/mahasiswa',
        summary: 'Daftar Semua Data Mahasiswa',
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
| | `data[].id` | `integer` | **Ya (Required)** | Id | `12` |
| | `data[].nim` | `integer` | **Ya (Required)** | Nim | `43322005` |
| | `data[].nama_mahasiswa` | `string` | **Ya (Required)** | Nama Mahasiswa | `"Bagas Wicaksono"` |
| | `data[].tahun_masuk` | `integer` | **Ya (Required)** | Tahun Masuk | `2022` |
| | `data[].jenis_pendaftaran` | `string` | **Ya (Required)** | Jenis Pendaftaran | `"Reguler"` |
| | `data[].tanggal_kelulusan` | `string` | **Ya (Required)** | Tanggal Kelulusan | `"2026-08-25"` |
| | `data[].masa_studi_bulan` | `integer` | **Ya (Required)** | Masa Studi Bulan | `48` |
| | `data[].created_at` | `string` | **Ya (Required)** | Created At | `"2026-10-19 03:13:34"` |
| | `data[].updated_at` | `string` | **Ya (Required)** | Updated At | `"2026-10-19 03:13:34"` |
| **401 Unauthorized** | `message` | `string` | **Ya (Required)** | Pesan otentikasi gagal saat token kosong atau kedaluwarsa | \\"Unauthenticated.\\" |
| **403 Forbidden** | `message` | `string` | **Ya (Required)** | Pesan penolakan izin akses Role/Permission | \\"User does not have the right permissions.\\" |
| **400 Bad Request** | `message` | `string` | **Ya (Required)** | Permintaan tidak valid atau data berelasi masih terikat | \\"Permintaan tidak valid\\" |',
        tags: ['Mahasiswa'],
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
        path: '/api/mahasiswa',
        summary: 'Tambah Data Mahasiswa Baru',
        description: '### 📋 1. Request Headers
| Header | Tipe Data | Wajib / Required | Keterangan | Contoh Nilai |
| :--- | :--- | :--- | :--- | :--- |
| `Authorization` | `string` | **Ya (Required)** | Token Bearer JWT hasil otentikasi login | `Bearer eyJhbGci...` |
| `Content-Type` | `string` | **Ya (Required)** | Tipe konten payload body permintaan | `application/json` |
| `Accept` | `string` | **Ya (Required)** | Tipe konten respon server yang diharapkan | `application/json` |

### 📥 2. Request Body
| Parameter | Tipe Data | Wajib / Required | Keterangan | Contoh Riil |
| :--- | :--- | :--- | :--- | :--- |
| `nim` | `integer` | **Ya (Required)** | Nomor Induk Mahasiswa (unik int) | `43322005` |
| `nama_mahasiswa` | `string` | **Ya (Required)** | Nama lengkap mahasiswa terdaftar | `Bagas Wicaksono` |
| `tahun_masuk` | `integer` | **Ya (Required)** | Tahun kalender mahasiswa pertama kali terdaftar | `2022` |
| `jenis_pendaftaran` | `string` | **Ya (Required)** | Jalur penerimaan mahasiswa (Reguler, SNBP, SNBT, Mandiri) | `Reguler` |
| `tanggal_kelulusan` | `string (date)` | *Tidak (Opsional / Nullable)* | Tanggal kelulusan resmi yudisium mahasiswa | `2026-08-25` |
| `masa_studi_bulan` | `integer` | **Ya (Required)** | Lama masa studi mahasiswa dalam satuan bulan | `48` |

### 📤 3. Struktur Response
| Status HTTP | Field / Properti | Tipe Data | Wajib / Required | Keterangan | Contoh Nilai |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **201 Created** | `success` | `boolean` | **Ya (Required)** | Indikator status keberhasilan (`true`) | `true` |
| | `code` | `integer` | **Ya (Required)** | HTTP Status Code (201) | `201` |
| | `message` | `string` | **Ya (Required)** | Pesan informasi respon server | "Data berhasil dibuat" |
| | `data` | `object` | **Ya (Required)** | Objek data entitas hasil operasi | `{ ... }` |
| | `data.id` | `integer` | **Ya (Required)** | Id | `12` |
| | `data.nim` | `integer` | **Ya (Required)** | Nomor Induk Mahasiswa (unik int) | `43322005` |
| | `data.nama_mahasiswa` | `string` | **Ya (Required)** | Nama lengkap mahasiswa terdaftar | `"Bagas Wicaksono"` |
| | `data.tahun_masuk` | `integer` | **Ya (Required)** | Tahun kalender mahasiswa pertama kali terdaftar | `2022` |
| | `data.jenis_pendaftaran` | `string` | **Ya (Required)** | Jalur penerimaan mahasiswa (Reguler, SNBP, SNBT, Mandiri) | `"Reguler"` |
| | `data.tanggal_kelulusan` | `string` | *Tidak (Opsional / Nullable)* | Tanggal kelulusan resmi yudisium mahasiswa | `"2026-08-25"` |
| | `data.masa_studi_bulan` | `integer` | **Ya (Required)** | Lama masa studi mahasiswa dalam satuan bulan | `48` |
| | `data.created_at` | `string` | **Ya (Required)** | Created At | `"2026-10-19 03:13:34"` |
| | `data.updated_at` | `string` | **Ya (Required)** | Updated At | `"2026-10-19 03:13:34"` |
| **422 Unprocessable** | `success` | `boolean` | **Ya (Required)** | Indikator kegagalan validasi (`false`) | `false` |
| | `code` | `integer` | **Ya (Required)** | HTTP Status Code (422) | `422` |
| | `message` | `string` | **Ya (Required)** | Ringkasan pesan kegagalan validasi | \\"The given data was invalid.\\" |
| | `errors` | `object` | **Ya (Required)** | Kamus pesan kesalahan validasi per field input | `{\\"field\\": [\\"Pesan error\\"]}` |
| **401 Unauthorized** | `message` | `string` | **Ya (Required)** | Pesan otentikasi gagal saat token kosong atau kedaluwarsa | \\"Unauthenticated.\\" |
| **403 Forbidden** | `message` | `string` | **Ya (Required)** | Pesan penolakan izin akses Role/Permission | \\"User does not have the right permissions.\\" |
| **400 Bad Request** | `message` | `string` | **Ya (Required)** | Permintaan tidak valid atau data berelasi masih terikat | \\"Permintaan tidak valid\\" |',
        tags: ['Mahasiswa'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['nim', 'nama_mahasiswa', 'tahun_masuk', 'jenis_pendaftaran', 'masa_studi_bulan'],
                properties: [
                    new OA\Property(property: 'nim', type: 'integer', example: 43322005, description: 'Nomor Induk Mahasiswa (unik int)'),
                    new OA\Property(property: 'nama_mahasiswa', type: 'string', example: 'Bagas Wicaksono', description: 'Nama lengkap mahasiswa terdaftar'),
                    new OA\Property(property: 'tahun_masuk', type: 'integer', example: 2022, description: 'Tahun kalender mahasiswa pertama kali terdaftar'),
                    new OA\Property(property: 'jenis_pendaftaran', type: 'string', example: 'Reguler', description: 'Jalur penerimaan mahasiswa (Reguler, SNBP, SNBT, Mandiri)'),
                    new OA\Property(property: 'tanggal_kelulusan', type: 'string', format: 'date', example: '2026-08-25', description: 'Tanggal kelulusan resmi yudisium mahasiswa', nullable: true),
                    new OA\Property(property: 'masa_studi_bulan', type: 'integer', example: 48, description: 'Lama masa studi mahasiswa dalam satuan bulan'),
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
        path: '/api/mahasiswa/{id}',
        summary: 'Detail Data Mahasiswa',
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
| | `data.id` | `integer` | **Ya (Required)** | Id | `12` |
| | `data.nim` | `integer` | **Ya (Required)** | Nim | `43322005` |
| | `data.nama_mahasiswa` | `string` | **Ya (Required)** | Nama Mahasiswa | `"Bagas Wicaksono"` |
| | `data.tahun_masuk` | `integer` | **Ya (Required)** | Tahun Masuk | `2022` |
| | `data.jenis_pendaftaran` | `string` | **Ya (Required)** | Jenis Pendaftaran | `"Reguler"` |
| | `data.tanggal_kelulusan` | `string` | **Ya (Required)** | Tanggal Kelulusan | `"2026-08-25"` |
| | `data.masa_studi_bulan` | `integer` | **Ya (Required)** | Masa Studi Bulan | `48` |
| | `data.created_at` | `string` | **Ya (Required)** | Created At | `"2026-10-19 03:13:34"` |
| | `data.updated_at` | `string` | **Ya (Required)** | Updated At | `"2026-10-19 03:13:34"` |
| **401 Unauthorized** | `message` | `string` | **Ya (Required)** | Pesan otentikasi gagal saat token kosong atau kedaluwarsa | \\"Unauthenticated.\\" |
| **403 Forbidden** | `message` | `string` | **Ya (Required)** | Pesan penolakan izin akses Role/Permission | \\"User does not have the right permissions.\\" |
| **404 Not Found** | `message` | `string` | **Ya (Required)** | Data referensi dengan ID yang diminta tidak ditemukan di database | \\"Data tidak ditemukan\\" |
| **400 Bad Request** | `message` | `string` | **Ya (Required)** | Permintaan tidak valid atau data berelasi masih terikat | \\"Permintaan tidak valid\\" |',
        tags: ['Mahasiswa'],
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
        path: '/api/mahasiswa/{id}',
        summary: 'Perbarui Data Mahasiswa',
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
| `nim` | `integer` | **Ya (Required)** | Nomor Induk Mahasiswa (unik int) | `43322005` |
| `nama_mahasiswa` | `string` | **Ya (Required)** | Nama lengkap mahasiswa terdaftar | `Bagas Wicaksono` |
| `tahun_masuk` | `integer` | **Ya (Required)** | Tahun kalender mahasiswa pertama kali terdaftar | `2022` |
| `jenis_pendaftaran` | `string` | **Ya (Required)** | Jalur penerimaan mahasiswa (Reguler, SNBP, SNBT, Mandiri) | `Reguler` |
| `tanggal_kelulusan` | `string (date)` | *Tidak (Opsional / Nullable)* | Tanggal kelulusan resmi yudisium mahasiswa | `2026-08-25` |
| `masa_studi_bulan` | `integer` | **Ya (Required)** | Lama masa studi mahasiswa dalam satuan bulan | `48` |

### 📤 4. Struktur Response
| Status HTTP | Field / Properti | Tipe Data | Wajib / Required | Keterangan | Contoh Nilai |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **200 OK** | `success` | `boolean` | **Ya (Required)** | Indikator status keberhasilan (`true`) | `true` |
| | `code` | `integer` | **Ya (Required)** | HTTP Status Code (200) | `200` |
| | `message` | `string` | **Ya (Required)** | Pesan informasi respon server | "Operasi berhasil" |
| | `data` | `object` | **Ya (Required)** | Objek data entitas hasil operasi | `{ ... }` |
| | `data.id` | `integer` | **Ya (Required)** | Id | `12` |
| | `data.nim` | `integer` | **Ya (Required)** | Nomor Induk Mahasiswa (unik int) | `43322005` |
| | `data.nama_mahasiswa` | `string` | **Ya (Required)** | Nama lengkap mahasiswa terdaftar | `"Bagas Wicaksono"` |
| | `data.tahun_masuk` | `integer` | **Ya (Required)** | Tahun kalender mahasiswa pertama kali terdaftar | `2022` |
| | `data.jenis_pendaftaran` | `string` | **Ya (Required)** | Jalur penerimaan mahasiswa (Reguler, SNBP, SNBT, Mandiri) | `"Reguler"` |
| | `data.tanggal_kelulusan` | `string` | *Tidak (Opsional / Nullable)* | Tanggal kelulusan resmi yudisium mahasiswa | `"2026-08-25"` |
| | `data.masa_studi_bulan` | `integer` | **Ya (Required)** | Lama masa studi mahasiswa dalam satuan bulan | `48` |
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
        tags: ['Mahasiswa'],
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
                required: ['nim', 'nama_mahasiswa', 'tahun_masuk', 'jenis_pendaftaran', 'masa_studi_bulan'],
                properties: [
                    new OA\Property(property: 'nim', type: 'integer', example: 43322005, description: 'Nomor Induk Mahasiswa (unik int)'),
                    new OA\Property(property: 'nama_mahasiswa', type: 'string', example: 'Bagas Wicaksono', description: 'Nama lengkap mahasiswa terdaftar'),
                    new OA\Property(property: 'tahun_masuk', type: 'integer', example: 2022, description: 'Tahun kalender mahasiswa pertama kali terdaftar'),
                    new OA\Property(property: 'jenis_pendaftaran', type: 'string', example: 'Reguler', description: 'Jalur penerimaan mahasiswa (Reguler, SNBP, SNBT, Mandiri)'),
                    new OA\Property(property: 'tanggal_kelulusan', type: 'string', format: 'date', example: '2026-08-25', description: 'Tanggal kelulusan resmi yudisium mahasiswa', nullable: true),
                    new OA\Property(property: 'masa_studi_bulan', type: 'integer', example: 48, description: 'Lama masa studi mahasiswa dalam satuan bulan'),
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
        path: '/api/mahasiswa/{id}',
        summary: 'Hapus Data Mahasiswa',
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
        tags: ['Mahasiswa'],
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
