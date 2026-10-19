<?php

namespace App\Docs;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Perguruan Tinggi', description: 'Endpoint API Perguruan Tinggi')]
class PerguruanTinggiDoc
{
    #[OA\Get(
        path: '/api/perguruan-tinggi',
        summary: 'Daftar Semua Data Perguruan Tinggi',
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
| | `data[].id` | `integer` | **Ya (Required)** | Id | `2` |
| | `data[].kode_pt` | `string` | **Ya (Required)** | Kode Pt | `"PT002"` |
| | `data[].nama_pt` | `string` | **Ya (Required)** | Nama Pt | `"Politeknik Elektronika Negeri Surabaya"` |
| | `data[].alamat` | `string` | **Ya (Required)** | Alamat | `"Jl. Raya ITS Kampus PENS Sukolilo, Surabaya 60111"` |
| | `data[].pimpinan` | `string` | **Ya (Required)** | Pimpinan | `"Ali Ridho Barakbah, S.Kom., Ph.D."` |
| | `data[].visi` | `string` | **Ya (Required)** | Visi | `"Menjadi pusat keunggulan pendidikan vokasi rekayasa dan teknologi terapan terdepan di tingkat global."` |
| | `data[].misi` | `string` | **Ya (Required)** | Misi | `"Mencetak sumber daya manusia berdaya saing tinggi dalam teknologi informasi dan elektronika."` |
| | `data[].created_at` | `string` | **Ya (Required)** | Created At | `"2026-10-19 03:13:32"` |
| | `data[].updated_at` | `string` | **Ya (Required)** | Updated At | `"2026-10-19 03:13:32"` |
| **401 Unauthorized** | `message` | `string` | **Ya (Required)** | Pesan otentikasi gagal saat token kosong atau kedaluwarsa | \\"Unauthenticated.\\" |
| **403 Forbidden** | `message` | `string` | **Ya (Required)** | Pesan penolakan izin akses Role/Permission | \\"User does not have the right permissions.\\" |
| **400 Bad Request** | `message` | `string` | **Ya (Required)** | Permintaan tidak valid atau data berelasi masih terikat | \\"Permintaan tidak valid\\" |',
        tags: ['Perguruan Tinggi'],
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
        path: '/api/perguruan-tinggi',
        summary: 'Tambah Data Perguruan Tinggi Baru',
        description: '### 📋 1. Request Headers
| Header | Tipe Data | Wajib / Required | Keterangan | Contoh Nilai |
| :--- | :--- | :--- | :--- | :--- |
| `Authorization` | `string` | **Ya (Required)** | Token Bearer JWT hasil otentikasi login | `Bearer eyJhbGci...` |
| `Content-Type` | `string` | **Ya (Required)** | Tipe konten payload body permintaan | `application/json` |
| `Accept` | `string` | **Ya (Required)** | Tipe konten respon server yang diharapkan | `application/json` |

### 📥 2. Request Body
| Parameter | Tipe Data | Wajib / Required | Keterangan | Contoh Riil |
| :--- | :--- | :--- | :--- | :--- |
| `kode_pt` | `string` | **Ya (Required)** | Kode unik identitas Perguruan Tinggi | `PT002` |
| `nama_pt` | `string` | **Ya (Required)** | Nama resmi Perguruan Tinggi | `Politeknik Elektronika Negeri Surabaya` |
| `alamat` | `string` | *Tidak (Opsional / Nullable)* | Alamat lengkap domisili kampus | `Jl. Raya ITS Kampus PENS Sukolilo, Surabaya 60111` |
| `pimpinan` | `string` | *Tidak (Opsional / Nullable)* | Nama pimpinan / Direktur Perguruan Tinggi | `Ali Ridho Barakbah, S.Kom., Ph.D.` |
| `visi` | `string` | *Tidak (Opsional / Nullable)* | Pernyataan visi Perguruan Tinggi | `Menjadi pusat keunggulan pendidikan vokasi rekayasa dan teknologi terapan terdepan di tingkat global.` |
| `misi` | `string` | *Tidak (Opsional / Nullable)* | Pernyataan misi Perguruan Tinggi | `Mencetak sumber daya manusia berdaya saing tinggi dalam teknologi informasi dan elektronika.` |

### 📤 3. Struktur Response
| Status HTTP | Field / Properti | Tipe Data | Wajib / Required | Keterangan | Contoh Nilai |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **201 Created** | `success` | `boolean` | **Ya (Required)** | Indikator status keberhasilan (`true`) | `true` |
| | `code` | `integer` | **Ya (Required)** | HTTP Status Code (201) | `201` |
| | `message` | `string` | **Ya (Required)** | Pesan informasi respon server | "Data berhasil dibuat" |
| | `data` | `object` | **Ya (Required)** | Objek data entitas hasil operasi | `{ ... }` |
| | `data.id` | `integer` | **Ya (Required)** | Id | `2` |
| | `data.kode_pt` | `string` | **Ya (Required)** | Kode unik identitas Perguruan Tinggi | `"PT002"` |
| | `data.nama_pt` | `string` | **Ya (Required)** | Nama resmi Perguruan Tinggi | `"Politeknik Elektronika Negeri Surabaya"` |
| | `data.alamat` | `string` | *Tidak (Opsional / Nullable)* | Alamat lengkap domisili kampus | `"Jl. Raya ITS Kampus PENS Sukolilo, Surabaya 60111"` |
| | `data.pimpinan` | `string` | *Tidak (Opsional / Nullable)* | Nama pimpinan / Direktur Perguruan Tinggi | `"Ali Ridho Barakbah, S.Kom., Ph.D."` |
| | `data.visi` | `string` | *Tidak (Opsional / Nullable)* | Pernyataan visi Perguruan Tinggi | `"Menjadi pusat keunggulan pendidikan vokasi rekayasa dan teknologi terapan terdepan di tingkat global."` |
| | `data.misi` | `string` | *Tidak (Opsional / Nullable)* | Pernyataan misi Perguruan Tinggi | `"Mencetak sumber daya manusia berdaya saing tinggi dalam teknologi informasi dan elektronika."` |
| | `data.created_at` | `string` | **Ya (Required)** | Created At | `"2026-10-19 03:13:32"` |
| | `data.updated_at` | `string` | **Ya (Required)** | Updated At | `"2026-10-19 03:13:32"` |
| **422 Unprocessable** | `success` | `boolean` | **Ya (Required)** | Indikator kegagalan validasi (`false`) | `false` |
| | `code` | `integer` | **Ya (Required)** | HTTP Status Code (422) | `422` |
| | `message` | `string` | **Ya (Required)** | Ringkasan pesan kegagalan validasi | \\"The given data was invalid.\\" |
| | `errors` | `object` | **Ya (Required)** | Kamus pesan kesalahan validasi per field input | `{\\"field\\": [\\"Pesan error\\"]}` |
| **401 Unauthorized** | `message` | `string` | **Ya (Required)** | Pesan otentikasi gagal saat token kosong atau kedaluwarsa | \\"Unauthenticated.\\" |
| **403 Forbidden** | `message` | `string` | **Ya (Required)** | Pesan penolakan izin akses Role/Permission | \\"User does not have the right permissions.\\" |
| **400 Bad Request** | `message` | `string` | **Ya (Required)** | Permintaan tidak valid atau data berelasi masih terikat | \\"Permintaan tidak valid\\" |',
        tags: ['Perguruan Tinggi'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['kode_pt', 'nama_pt'],
                properties: [
                    new OA\Property(property: 'kode_pt', type: 'string', example: 'PT002', description: 'Kode unik identitas Perguruan Tinggi'),
                    new OA\Property(property: 'nama_pt', type: 'string', example: 'Politeknik Elektronika Negeri Surabaya', description: 'Nama resmi Perguruan Tinggi'),
                    new OA\Property(property: 'alamat', type: 'string', example: 'Jl. Raya ITS Kampus PENS Sukolilo, Surabaya 60111', description: 'Alamat lengkap domisili kampus', nullable: true),
                    new OA\Property(property: 'pimpinan', type: 'string', example: 'Ali Ridho Barakbah, S.Kom., Ph.D.', description: 'Nama pimpinan / Direktur Perguruan Tinggi', nullable: true),
                    new OA\Property(property: 'visi', type: 'string', example: 'Menjadi pusat keunggulan pendidikan vokasi rekayasa dan teknologi terapan terdepan di tingkat global.', description: 'Pernyataan visi Perguruan Tinggi', nullable: true),
                    new OA\Property(property: 'misi', type: 'string', example: 'Mencetak sumber daya manusia berdaya saing tinggi dalam teknologi informasi dan elektronika.', description: 'Pernyataan misi Perguruan Tinggi', nullable: true),
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
        path: '/api/perguruan-tinggi/{id}',
        summary: 'Detail Data Perguruan Tinggi',
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
| | `data.id` | `integer` | **Ya (Required)** | Id | `2` |
| | `data.kode_pt` | `string` | **Ya (Required)** | Kode Pt | `"PT002"` |
| | `data.nama_pt` | `string` | **Ya (Required)** | Nama Pt | `"Politeknik Elektronika Negeri Surabaya"` |
| | `data.alamat` | `string` | **Ya (Required)** | Alamat | `"Jl. Raya ITS Kampus PENS Sukolilo, Surabaya 60111"` |
| | `data.pimpinan` | `string` | **Ya (Required)** | Pimpinan | `"Ali Ridho Barakbah, S.Kom., Ph.D."` |
| | `data.visi` | `string` | **Ya (Required)** | Visi | `"Menjadi pusat keunggulan pendidikan vokasi rekayasa dan teknologi terapan terdepan di tingkat global."` |
| | `data.misi` | `string` | **Ya (Required)** | Misi | `"Mencetak sumber daya manusia berdaya saing tinggi dalam teknologi informasi dan elektronika."` |
| | `data.created_at` | `string` | **Ya (Required)** | Created At | `"2026-10-19 03:13:32"` |
| | `data.updated_at` | `string` | **Ya (Required)** | Updated At | `"2026-10-19 03:13:32"` |
| **401 Unauthorized** | `message` | `string` | **Ya (Required)** | Pesan otentikasi gagal saat token kosong atau kedaluwarsa | \\"Unauthenticated.\\" |
| **403 Forbidden** | `message` | `string` | **Ya (Required)** | Pesan penolakan izin akses Role/Permission | \\"User does not have the right permissions.\\" |
| **404 Not Found** | `message` | `string` | **Ya (Required)** | Data referensi dengan ID yang diminta tidak ditemukan di database | \\"Data tidak ditemukan\\" |
| **400 Bad Request** | `message` | `string` | **Ya (Required)** | Permintaan tidak valid atau data berelasi masih terikat | \\"Permintaan tidak valid\\" |',
        tags: ['Perguruan Tinggi'],
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
        path: '/api/perguruan-tinggi/{id}',
        summary: 'Perbarui Data Perguruan Tinggi',
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
| `kode_pt` | `string` | **Ya (Required)** | Kode unik identitas Perguruan Tinggi | `PT002` |
| `nama_pt` | `string` | **Ya (Required)** | Nama resmi Perguruan Tinggi | `Politeknik Elektronika Negeri Surabaya` |
| `alamat` | `string` | *Tidak (Opsional / Nullable)* | Alamat lengkap domisili kampus | `Jl. Raya ITS Kampus PENS Sukolilo, Surabaya 60111` |
| `pimpinan` | `string` | *Tidak (Opsional / Nullable)* | Nama pimpinan / Direktur Perguruan Tinggi | `Ali Ridho Barakbah, S.Kom., Ph.D.` |
| `visi` | `string` | *Tidak (Opsional / Nullable)* | Pernyataan visi Perguruan Tinggi | `Menjadi pusat keunggulan pendidikan vokasi rekayasa dan teknologi terapan terdepan di tingkat global.` |
| `misi` | `string` | *Tidak (Opsional / Nullable)* | Pernyataan misi Perguruan Tinggi | `Mencetak sumber daya manusia berdaya saing tinggi dalam teknologi informasi dan elektronika.` |

### 📤 4. Struktur Response
| Status HTTP | Field / Properti | Tipe Data | Wajib / Required | Keterangan | Contoh Nilai |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **200 OK** | `success` | `boolean` | **Ya (Required)** | Indikator status keberhasilan (`true`) | `true` |
| | `code` | `integer` | **Ya (Required)** | HTTP Status Code (200) | `200` |
| | `message` | `string` | **Ya (Required)** | Pesan informasi respon server | "Operasi berhasil" |
| | `data` | `object` | **Ya (Required)** | Objek data entitas hasil operasi | `{ ... }` |
| | `data.id` | `integer` | **Ya (Required)** | Id | `2` |
| | `data.kode_pt` | `string` | **Ya (Required)** | Kode unik identitas Perguruan Tinggi | `"PT002"` |
| | `data.nama_pt` | `string` | **Ya (Required)** | Nama resmi Perguruan Tinggi | `"Politeknik Elektronika Negeri Surabaya"` |
| | `data.alamat` | `string` | *Tidak (Opsional / Nullable)* | Alamat lengkap domisili kampus | `"Jl. Raya ITS Kampus PENS Sukolilo, Surabaya 60111"` |
| | `data.pimpinan` | `string` | *Tidak (Opsional / Nullable)* | Nama pimpinan / Direktur Perguruan Tinggi | `"Ali Ridho Barakbah, S.Kom., Ph.D."` |
| | `data.visi` | `string` | *Tidak (Opsional / Nullable)* | Pernyataan visi Perguruan Tinggi | `"Menjadi pusat keunggulan pendidikan vokasi rekayasa dan teknologi terapan terdepan di tingkat global."` |
| | `data.misi` | `string` | *Tidak (Opsional / Nullable)* | Pernyataan misi Perguruan Tinggi | `"Mencetak sumber daya manusia berdaya saing tinggi dalam teknologi informasi dan elektronika."` |
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
        tags: ['Perguruan Tinggi'],
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
                required: ['kode_pt', 'nama_pt'],
                properties: [
                    new OA\Property(property: 'kode_pt', type: 'string', example: 'PT002', description: 'Kode unik identitas Perguruan Tinggi'),
                    new OA\Property(property: 'nama_pt', type: 'string', example: 'Politeknik Elektronika Negeri Surabaya', description: 'Nama resmi Perguruan Tinggi'),
                    new OA\Property(property: 'alamat', type: 'string', example: 'Jl. Raya ITS Kampus PENS Sukolilo, Surabaya 60111', description: 'Alamat lengkap domisili kampus', nullable: true),
                    new OA\Property(property: 'pimpinan', type: 'string', example: 'Ali Ridho Barakbah, S.Kom., Ph.D.', description: 'Nama pimpinan / Direktur Perguruan Tinggi', nullable: true),
                    new OA\Property(property: 'visi', type: 'string', example: 'Menjadi pusat keunggulan pendidikan vokasi rekayasa dan teknologi terapan terdepan di tingkat global.', description: 'Pernyataan visi Perguruan Tinggi', nullable: true),
                    new OA\Property(property: 'misi', type: 'string', example: 'Mencetak sumber daya manusia berdaya saing tinggi dalam teknologi informasi dan elektronika.', description: 'Pernyataan misi Perguruan Tinggi', nullable: true),
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
        path: '/api/perguruan-tinggi/{id}',
        summary: 'Hapus Data Perguruan Tinggi',
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
        tags: ['Perguruan Tinggi'],
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
