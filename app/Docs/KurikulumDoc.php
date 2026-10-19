<?php

namespace App\Docs;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Kurikulum', description: 'Endpoint API Kurikulum')]
class KurikulumDoc
{
    #[OA\Get(
        path: '/api/kurikulum',
        summary: 'Daftar Semua Data Kurikulum',
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
| | `data[].id_prodi` | `integer` | **Ya (Required)** | Id Prodi | `2` |
| | `data[].nama_kurikulum` | `string` | **Ya (Required)** | Nama Kurikulum | `"Kurikulum D3 Teknik Informatika 2023"` |
| | `data[].berlaku_sampai` | `integer` | **Ya (Required)** | Berlaku Sampai | `2027` |
| | `data[].sk_kurikulum` | `string` | **Ya (Required)** | Sk Kurikulum | `"SK/2023/008/TI"` |
| | `data[].created_at` | `string` | **Ya (Required)** | Created At | `"2026-10-19 03:13:33"` |
| | `data[].updated_at` | `string` | **Ya (Required)** | Updated At | `"2026-10-19 03:13:33"` |
| **401 Unauthorized** | `message` | `string` | **Ya (Required)** | Pesan otentikasi gagal saat token kosong atau kedaluwarsa | \\"Unauthenticated.\\" |
| **403 Forbidden** | `message` | `string` | **Ya (Required)** | Pesan penolakan izin akses Role/Permission | \\"User does not have the right permissions.\\" |
| **400 Bad Request** | `message` | `string` | **Ya (Required)** | Permintaan tidak valid atau data berelasi masih terikat | \\"Permintaan tidak valid\\" |',
        tags: ['Kurikulum'],
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
        path: '/api/kurikulum',
        summary: 'Tambah Data Kurikulum Baru',
        description: '### 📋 1. Request Headers
| Header | Tipe Data | Wajib / Required | Keterangan | Contoh Nilai |
| :--- | :--- | :--- | :--- | :--- |
| `Authorization` | `string` | **Ya (Required)** | Token Bearer JWT hasil otentikasi login | `Bearer eyJhbGci...` |
| `Content-Type` | `string` | **Ya (Required)** | Tipe konten payload body permintaan | `application/json` |
| `Accept` | `string` | **Ya (Required)** | Tipe konten respon server yang diharapkan | `application/json` |

### 📥 2. Request Body
| Parameter | Tipe Data | Wajib / Required | Keterangan | Contoh Riil |
| :--- | :--- | :--- | :--- | :--- |
| `id_prodi` | `integer` | **Ya (Required)** | ID referensi Program Studi | `2` |
| `nama_kurikulum` | `string` | **Ya (Required)** | Nama kurikulum akademik | `Kurikulum D3 Teknik Informatika 2023` |
| `berlaku_sampai` | `integer` | **Ya (Required)** | Tahun masa berakhirnya kurikulum | `2027` |
| `sk_kurikulum` | `string` | *Tidak (Opsional / Nullable)* | Nomor Surat Keputusan (SK) pemberlakuan kurikulum | `SK/2023/008/TI` |

### 📤 3. Struktur Response
| Status HTTP | Field / Properti | Tipe Data | Wajib / Required | Keterangan | Contoh Nilai |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **201 Created** | `success` | `boolean` | **Ya (Required)** | Indikator status keberhasilan (`true`) | `true` |
| | `code` | `integer` | **Ya (Required)** | HTTP Status Code (201) | `201` |
| | `message` | `string` | **Ya (Required)** | Pesan informasi respon server | "Data berhasil dibuat" |
| | `data` | `object` | **Ya (Required)** | Objek data entitas hasil operasi | `{ ... }` |
| | `data.id` | `integer` | **Ya (Required)** | Id | `3` |
| | `data.id_prodi` | `integer` | **Ya (Required)** | ID referensi Program Studi | `2` |
| | `data.nama_kurikulum` | `string` | **Ya (Required)** | Nama kurikulum akademik | `"Kurikulum D3 Teknik Informatika 2023"` |
| | `data.berlaku_sampai` | `integer` | **Ya (Required)** | Tahun masa berakhirnya kurikulum | `2027` |
| | `data.sk_kurikulum` | `string` | *Tidak (Opsional / Nullable)* | Nomor Surat Keputusan (SK) pemberlakuan kurikulum | `"SK/2023/008/TI"` |
| | `data.created_at` | `string` | **Ya (Required)** | Created At | `"2026-10-19 03:13:33"` |
| | `data.updated_at` | `string` | **Ya (Required)** | Updated At | `"2026-10-19 03:13:33"` |
| **422 Unprocessable** | `success` | `boolean` | **Ya (Required)** | Indikator kegagalan validasi (`false`) | `false` |
| | `code` | `integer` | **Ya (Required)** | HTTP Status Code (422) | `422` |
| | `message` | `string` | **Ya (Required)** | Ringkasan pesan kegagalan validasi | \\"The given data was invalid.\\" |
| | `errors` | `object` | **Ya (Required)** | Kamus pesan kesalahan validasi per field input | `{\\"field\\": [\\"Pesan error\\"]}` |
| **401 Unauthorized** | `message` | `string` | **Ya (Required)** | Pesan otentikasi gagal saat token kosong atau kedaluwarsa | \\"Unauthenticated.\\" |
| **403 Forbidden** | `message` | `string` | **Ya (Required)** | Pesan penolakan izin akses Role/Permission | \\"User does not have the right permissions.\\" |
| **400 Bad Request** | `message` | `string` | **Ya (Required)** | Permintaan tidak valid atau data berelasi masih terikat | \\"Permintaan tidak valid\\" |',
        tags: ['Kurikulum'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['id_prodi', 'nama_kurikulum', 'berlaku_sampai'],
                properties: [
                    new OA\Property(property: 'id_prodi', type: 'integer', example: 2, description: 'ID referensi Program Studi'),
                    new OA\Property(property: 'nama_kurikulum', type: 'string', example: 'Kurikulum D3 Teknik Informatika 2023', description: 'Nama kurikulum akademik'),
                    new OA\Property(property: 'berlaku_sampai', type: 'integer', example: 2027, description: 'Tahun masa berakhirnya kurikulum'),
                    new OA\Property(property: 'sk_kurikulum', type: 'string', example: 'SK/2023/008/TI', description: 'Nomor Surat Keputusan (SK) pemberlakuan kurikulum', nullable: true),
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
        path: '/api/kurikulum/{id}',
        summary: 'Detail Data Kurikulum',
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
| | `data.id_prodi` | `integer` | **Ya (Required)** | Id Prodi | `2` |
| | `data.nama_kurikulum` | `string` | **Ya (Required)** | Nama Kurikulum | `"Kurikulum D3 Teknik Informatika 2023"` |
| | `data.berlaku_sampai` | `integer` | **Ya (Required)** | Berlaku Sampai | `2027` |
| | `data.sk_kurikulum` | `string` | **Ya (Required)** | Sk Kurikulum | `"SK/2023/008/TI"` |
| | `data.created_at` | `string` | **Ya (Required)** | Created At | `"2026-10-19 03:13:33"` |
| | `data.updated_at` | `string` | **Ya (Required)** | Updated At | `"2026-10-19 03:13:33"` |
| **401 Unauthorized** | `message` | `string` | **Ya (Required)** | Pesan otentikasi gagal saat token kosong atau kedaluwarsa | \\"Unauthenticated.\\" |
| **403 Forbidden** | `message` | `string` | **Ya (Required)** | Pesan penolakan izin akses Role/Permission | \\"User does not have the right permissions.\\" |
| **404 Not Found** | `message` | `string` | **Ya (Required)** | Data referensi dengan ID yang diminta tidak ditemukan di database | \\"Data tidak ditemukan\\" |
| **400 Bad Request** | `message` | `string` | **Ya (Required)** | Permintaan tidak valid atau data berelasi masih terikat | \\"Permintaan tidak valid\\" |',
        tags: ['Kurikulum'],
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
        path: '/api/kurikulum/{id}',
        summary: 'Perbarui Data Kurikulum',
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
| `id_prodi` | `integer` | **Ya (Required)** | ID referensi Program Studi | `2` |
| `nama_kurikulum` | `string` | **Ya (Required)** | Nama kurikulum akademik | `Kurikulum D3 Teknik Informatika 2023` |
| `berlaku_sampai` | `integer` | **Ya (Required)** | Tahun masa berakhirnya kurikulum | `2027` |
| `sk_kurikulum` | `string` | *Tidak (Opsional / Nullable)* | Nomor Surat Keputusan (SK) pemberlakuan kurikulum | `SK/2023/008/TI` |

### 📤 4. Struktur Response
| Status HTTP | Field / Properti | Tipe Data | Wajib / Required | Keterangan | Contoh Nilai |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **200 OK** | `success` | `boolean` | **Ya (Required)** | Indikator status keberhasilan (`true`) | `true` |
| | `code` | `integer` | **Ya (Required)** | HTTP Status Code (200) | `200` |
| | `message` | `string` | **Ya (Required)** | Pesan informasi respon server | "Operasi berhasil" |
| | `data` | `object` | **Ya (Required)** | Objek data entitas hasil operasi | `{ ... }` |
| | `data.id` | `integer` | **Ya (Required)** | Id | `3` |
| | `data.id_prodi` | `integer` | **Ya (Required)** | ID referensi Program Studi | `2` |
| | `data.nama_kurikulum` | `string` | **Ya (Required)** | Nama kurikulum akademik | `"Kurikulum D3 Teknik Informatika 2023"` |
| | `data.berlaku_sampai` | `integer` | **Ya (Required)** | Tahun masa berakhirnya kurikulum | `2027` |
| | `data.sk_kurikulum` | `string` | *Tidak (Opsional / Nullable)* | Nomor Surat Keputusan (SK) pemberlakuan kurikulum | `"SK/2023/008/TI"` |
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
        tags: ['Kurikulum'],
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
                required: ['id_prodi', 'nama_kurikulum', 'berlaku_sampai'],
                properties: [
                    new OA\Property(property: 'id_prodi', type: 'integer', example: 2, description: 'ID referensi Program Studi'),
                    new OA\Property(property: 'nama_kurikulum', type: 'string', example: 'Kurikulum D3 Teknik Informatika 2023', description: 'Nama kurikulum akademik'),
                    new OA\Property(property: 'berlaku_sampai', type: 'integer', example: 2027, description: 'Tahun masa berakhirnya kurikulum'),
                    new OA\Property(property: 'sk_kurikulum', type: 'string', example: 'SK/2023/008/TI', description: 'Nomor Surat Keputusan (SK) pemberlakuan kurikulum', nullable: true),
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
        path: '/api/kurikulum/{id}',
        summary: 'Hapus Data Kurikulum',
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
        tags: ['Kurikulum'],
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
