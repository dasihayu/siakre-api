<?php

namespace App\Docs;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Kegiatan Mahasiswa', description: 'Endpoint API Kegiatan Mahasiswa')]
class KegiatanMahasiswaDoc
{
    #[OA\Get(
        path: '/api/kegiatan-mahasiswa',
        summary: 'Daftar Semua Data Kegiatan Mahasiswa',
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
| | `data[].id` | `integer` | **Ya (Required)** | Id | `9` |
| | `data[].mahasiswa_id` | `integer` | **Ya (Required)** | Mahasiswa Id | `1` |
| | `data[].tahun_akademik_id` | `integer` | **Ya (Required)** | Tahun Akademik Id | `4` |
| | `data[].nama_kegiatan` | `string` | **Ya (Required)** | Nama Kegiatan | `"Turnamen Mobile Legends"` |
| | `data[].jenis_kegiatan` | `string` | **Ya (Required)** | Jenis Kegiatan | `"UKM"` |
| | `data[].peran` | `string` | **Ya (Required)** | Peran | `"Ketua Tim"` |
| | `data[].tingkat` | `string` | **Ya (Required)** | Tingkat | `"Polines"` |
| | `data[].tanggal_mulai` | `string` | **Ya (Required)** | Tanggal Mulai | `"2024-02-01"` |
| | `data[].tanggal_selesai` | `string` | **Ya (Required)** | Tanggal Selesai | `"2024-06-30"` |
| | `data[].penyelenggara` | `string` | **Ya (Required)** | Penyelenggara | `"BEM Polines"` |
| | `data[].status_validasi` | `string` | **Ya (Required)** | Status Validasi | `"Disetujui"` |
| | `data[].created_at` | `string` | **Ya (Required)** | Created At | `"2026-10-19 03:13:35"` |
| | `data[].updated_at` | `string` | **Ya (Required)** | Updated At | `"2026-10-19 03:13:35"` |
| **401 Unauthorized** | `message` | `string` | **Ya (Required)** | Pesan otentikasi gagal saat token kosong atau kedaluwarsa | \\"Unauthenticated.\\" |
| **403 Forbidden** | `message` | `string` | **Ya (Required)** | Pesan penolakan izin akses Role/Permission | \\"User does not have the right permissions.\\" |
| **400 Bad Request** | `message` | `string` | **Ya (Required)** | Permintaan tidak valid atau data berelasi masih terikat | \\"Permintaan tidak valid\\" |',
        tags: ['Kegiatan Mahasiswa'],
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
        path: '/api/kegiatan-mahasiswa',
        summary: 'Tambah Data Kegiatan Mahasiswa Baru',
        description: '### 📋 1. Request Headers
| Header | Tipe Data | Wajib / Required | Keterangan | Contoh Nilai |
| :--- | :--- | :--- | :--- | :--- |
| `Authorization` | `string` | **Ya (Required)** | Token Bearer JWT hasil otentikasi login | `Bearer eyJhbGci...` |
| `Content-Type` | `string` | **Ya (Required)** | Tipe konten payload body permintaan | `application/json` |
| `Accept` | `string` | **Ya (Required)** | Tipe konten respon server yang diharapkan | `application/json` |

### 📥 2. Request Body
| Parameter | Tipe Data | Wajib / Required | Keterangan | Contoh Riil |
| :--- | :--- | :--- | :--- | :--- |
| `mahasiswa_id` | `integer` | **Ya (Required)** | ID referensi Mahasiswa | `1` |
| `tahun_akademik_id` | `integer` | **Ya (Required)** | ID referensi Tahun Akademik | `4` |
| `nama_kegiatan` | `string` | **Ya (Required)** | Nama kegiatan kemahasiswaan atau lomba | `Turnamen Mobile Legends` |
| `jenis_kegiatan` | `string` | **Ya (Required)** | Jenis kegiatan (Organisasi, Lomba, Seminar, Magang, UKM) | `UKM` |
| `peran` | `string` | **Ya (Required)** | Peran atau posisi mahasiswa dalam kegiatan | `Ketua Tim` |
| `tingkat` | `string` | **Ya (Required)** | Skala lingkup kegiatan (Jurusan, Polines, Regional, Nasional, Internasional) | `Polines` |
| `tanggal_mulai` | `string (date)` | **Ya (Required)** | Tanggal pelaksanaan kegiatan dimulai | `2024-02-01` |
| `tanggal_selesai` | `string (date)` | *Tidak (Opsional / Nullable)* | Tanggal pelaksanaan kegiatan selesai | `2024-06-30` |
| `penyelenggara` | `string` | **Ya (Required)** | Instansi atau organisasi penyelenggara kegiatan | `BEM Polines` |
| `status_validasi` | `string` | **Ya (Required)** | Status persetujuan validasi (Disetujui, Pending, Ditolak) | `Disetujui` |

### 📤 3. Struktur Response
| Status HTTP | Field / Properti | Tipe Data | Wajib / Required | Keterangan | Contoh Nilai |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **201 Created** | `success` | `boolean` | **Ya (Required)** | Indikator status keberhasilan (`true`) | `true` |
| | `code` | `integer` | **Ya (Required)** | HTTP Status Code (201) | `201` |
| | `message` | `string` | **Ya (Required)** | Pesan informasi respon server | "Data berhasil dibuat" |
| | `data` | `object` | **Ya (Required)** | Objek data entitas hasil operasi | `{ ... }` |
| | `data.id` | `integer` | **Ya (Required)** | Id | `9` |
| | `data.mahasiswa_id` | `integer` | **Ya (Required)** | ID referensi Mahasiswa | `1` |
| | `data.tahun_akademik_id` | `integer` | **Ya (Required)** | ID referensi Tahun Akademik | `4` |
| | `data.nama_kegiatan` | `string` | **Ya (Required)** | Nama kegiatan kemahasiswaan atau lomba | `"Turnamen Mobile Legends"` |
| | `data.jenis_kegiatan` | `string` | **Ya (Required)** | Jenis kegiatan (Organisasi, Lomba, Seminar, Magang, UKM) | `"UKM"` |
| | `data.peran` | `string` | **Ya (Required)** | Peran atau posisi mahasiswa dalam kegiatan | `"Ketua Tim"` |
| | `data.tingkat` | `string` | **Ya (Required)** | Skala lingkup kegiatan (Jurusan, Polines, Regional, Nasional, Internasional) | `"Polines"` |
| | `data.tanggal_mulai` | `string` | **Ya (Required)** | Tanggal pelaksanaan kegiatan dimulai | `"2024-02-01"` |
| | `data.tanggal_selesai` | `string` | *Tidak (Opsional / Nullable)* | Tanggal pelaksanaan kegiatan selesai | `"2024-06-30"` |
| | `data.penyelenggara` | `string` | **Ya (Required)** | Instansi atau organisasi penyelenggara kegiatan | `"BEM Polines"` |
| | `data.status_validasi` | `string` | **Ya (Required)** | Status persetujuan validasi (Disetujui, Pending, Ditolak) | `"Disetujui"` |
| | `data.created_at` | `string` | **Ya (Required)** | Created At | `"2026-10-19 03:13:35"` |
| | `data.updated_at` | `string` | **Ya (Required)** | Updated At | `"2026-10-19 03:13:35"` |
| **422 Unprocessable** | `success` | `boolean` | **Ya (Required)** | Indikator kegagalan validasi (`false`) | `false` |
| | `code` | `integer` | **Ya (Required)** | HTTP Status Code (422) | `422` |
| | `message` | `string` | **Ya (Required)** | Ringkasan pesan kegagalan validasi | \\"The given data was invalid.\\" |
| | `errors` | `object` | **Ya (Required)** | Kamus pesan kesalahan validasi per field input | `{\\"field\\": [\\"Pesan error\\"]}` |
| **401 Unauthorized** | `message` | `string` | **Ya (Required)** | Pesan otentikasi gagal saat token kosong atau kedaluwarsa | \\"Unauthenticated.\\" |
| **403 Forbidden** | `message` | `string` | **Ya (Required)** | Pesan penolakan izin akses Role/Permission | \\"User does not have the right permissions.\\" |
| **400 Bad Request** | `message` | `string` | **Ya (Required)** | Permintaan tidak valid atau data berelasi masih terikat | \\"Permintaan tidak valid\\" |',
        tags: ['Kegiatan Mahasiswa'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['mahasiswa_id', 'tahun_akademik_id', 'nama_kegiatan', 'jenis_kegiatan', 'peran', 'tingkat', 'tanggal_mulai', 'penyelenggara', 'status_validasi'],
                properties: [
                    new OA\Property(property: 'mahasiswa_id', type: 'integer', example: 1, description: 'ID referensi Mahasiswa'),
                    new OA\Property(property: 'tahun_akademik_id', type: 'integer', example: 4, description: 'ID referensi Tahun Akademik'),
                    new OA\Property(property: 'nama_kegiatan', type: 'string', example: 'Turnamen Mobile Legends', description: 'Nama kegiatan kemahasiswaan atau lomba'),
                    new OA\Property(property: 'jenis_kegiatan', type: 'string', example: 'UKM', description: 'Jenis kegiatan (Organisasi, Lomba, Seminar, Magang, UKM)'),
                    new OA\Property(property: 'peran', type: 'string', example: 'Ketua Tim', description: 'Peran atau posisi mahasiswa dalam kegiatan'),
                    new OA\Property(property: 'tingkat', type: 'string', example: 'Polines', description: 'Skala lingkup kegiatan (Jurusan, Polines, Regional, Nasional, Internasional)'),
                    new OA\Property(property: 'tanggal_mulai', type: 'string', format: 'date', example: '2024-02-01', description: 'Tanggal pelaksanaan kegiatan dimulai'),
                    new OA\Property(property: 'tanggal_selesai', type: 'string', format: 'date', example: '2024-06-30', description: 'Tanggal pelaksanaan kegiatan selesai', nullable: true),
                    new OA\Property(property: 'penyelenggara', type: 'string', example: 'BEM Polines', description: 'Instansi atau organisasi penyelenggara kegiatan'),
                    new OA\Property(property: 'status_validasi', type: 'string', example: 'Disetujui', description: 'Status persetujuan validasi (Disetujui, Pending, Ditolak)'),
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
        path: '/api/kegiatan-mahasiswa/{id}',
        summary: 'Detail Data Kegiatan Mahasiswa',
        description: '### 📋 1. Request Headers
| Header | Tipe Data | Wajib / Required | Keterangan | Contoh Nilai |
| :--- | :--- | :--- | :--- | :--- |
| `Authorization` | `string` | **Ya (Required)** | Token Bearer JWT hasil otentikasi login | `Bearer eyJhbGci...` |
| `Accept` | `string` | **Ya (Required)** | Tipe konten respon server yang diharapkan | `application/json` |

### 🔍 2. Path Parameters
| Parameter | Tipe Data | Wajib / Required | Keterangan | Contoh Nilai |
| :--- | :--- | :--- | :--- | :--- |
| `id` | `integer` | **Ya (Required)** | Parameter ID unik id pada path URL | `9` |

### 📤 3. Struktur Response
| Status HTTP | Field / Properti | Tipe Data | Wajib / Required | Keterangan | Contoh Nilai |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **200 OK** | `success` | `boolean` | **Ya (Required)** | Indikator status keberhasilan (`true`) | `true` |
| | `code` | `integer` | **Ya (Required)** | HTTP Status Code (200) | `200` |
| | `message` | `string` | **Ya (Required)** | Pesan informasi respon server | "Operasi berhasil" |
| | `data` | `object` | **Ya (Required)** | Objek data entitas hasil operasi | `{ ... }` |
| | `data.id` | `integer` | **Ya (Required)** | Id | `9` |
| | `data.mahasiswa_id` | `integer` | **Ya (Required)** | Mahasiswa Id | `1` |
| | `data.tahun_akademik_id` | `integer` | **Ya (Required)** | Tahun Akademik Id | `4` |
| | `data.nama_kegiatan` | `string` | **Ya (Required)** | Nama Kegiatan | `"Turnamen Mobile Legends"` |
| | `data.jenis_kegiatan` | `string` | **Ya (Required)** | Jenis Kegiatan | `"UKM"` |
| | `data.peran` | `string` | **Ya (Required)** | Peran | `"Ketua Tim"` |
| | `data.tingkat` | `string` | **Ya (Required)** | Tingkat | `"Polines"` |
| | `data.tanggal_mulai` | `string` | **Ya (Required)** | Tanggal Mulai | `"2024-02-01"` |
| | `data.tanggal_selesai` | `string` | **Ya (Required)** | Tanggal Selesai | `"2024-06-30"` |
| | `data.penyelenggara` | `string` | **Ya (Required)** | Penyelenggara | `"BEM Polines"` |
| | `data.status_validasi` | `string` | **Ya (Required)** | Status Validasi | `"Disetujui"` |
| | `data.created_at` | `string` | **Ya (Required)** | Created At | `"2026-10-19 03:13:35"` |
| | `data.updated_at` | `string` | **Ya (Required)** | Updated At | `"2026-10-19 03:13:35"` |
| **401 Unauthorized** | `message` | `string` | **Ya (Required)** | Pesan otentikasi gagal saat token kosong atau kedaluwarsa | \\"Unauthenticated.\\" |
| **403 Forbidden** | `message` | `string` | **Ya (Required)** | Pesan penolakan izin akses Role/Permission | \\"User does not have the right permissions.\\" |
| **404 Not Found** | `message` | `string` | **Ya (Required)** | Data referensi dengan ID yang diminta tidak ditemukan di database | \\"Data tidak ditemukan\\" |
| **400 Bad Request** | `message` | `string` | **Ya (Required)** | Permintaan tidak valid atau data berelasi masih terikat | \\"Permintaan tidak valid\\" |',
        tags: ['Kegiatan Mahasiswa'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID data id',
                schema: new OA\Schema(type: 'integer', example: 9)
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
        path: '/api/kegiatan-mahasiswa/{id}',
        summary: 'Perbarui Data Kegiatan Mahasiswa',
        description: '### 📋 1. Request Headers
| Header | Tipe Data | Wajib / Required | Keterangan | Contoh Nilai |
| :--- | :--- | :--- | :--- | :--- |
| `Authorization` | `string` | **Ya (Required)** | Token Bearer JWT hasil otentikasi login | `Bearer eyJhbGci...` |
| `Content-Type` | `string` | **Ya (Required)** | Tipe konten payload body permintaan | `application/json` |
| `Accept` | `string` | **Ya (Required)** | Tipe konten respon server yang diharapkan | `application/json` |

### 🔍 2. Path Parameters
| Parameter | Tipe Data | Wajib / Required | Keterangan | Contoh Nilai |
| :--- | :--- | :--- | :--- | :--- |
| `id` | `integer` | **Ya (Required)** | Parameter ID unik id pada path URL | `9` |

### 📥 3. Request Body
| Parameter | Tipe Data | Wajib / Required | Keterangan | Contoh Riil |
| :--- | :--- | :--- | :--- | :--- |
| `mahasiswa_id` | `integer` | **Ya (Required)** | ID referensi Mahasiswa | `1` |
| `tahun_akademik_id` | `integer` | **Ya (Required)** | ID referensi Tahun Akademik | `4` |
| `nama_kegiatan` | `string` | **Ya (Required)** | Nama kegiatan kemahasiswaan atau lomba | `Turnamen Mobile Legends` |
| `jenis_kegiatan` | `string` | **Ya (Required)** | Jenis kegiatan (Organisasi, Lomba, Seminar, Magang, UKM) | `UKM` |
| `peran` | `string` | **Ya (Required)** | Peran atau posisi mahasiswa dalam kegiatan | `Ketua Tim` |
| `tingkat` | `string` | **Ya (Required)** | Skala lingkup kegiatan (Jurusan, Polines, Regional, Nasional, Internasional) | `Polines` |
| `tanggal_mulai` | `string (date)` | **Ya (Required)** | Tanggal pelaksanaan kegiatan dimulai | `2024-02-01` |
| `tanggal_selesai` | `string (date)` | *Tidak (Opsional / Nullable)* | Tanggal pelaksanaan kegiatan selesai | `2024-06-30` |
| `penyelenggara` | `string` | **Ya (Required)** | Instansi atau organisasi penyelenggara kegiatan | `BEM Polines` |
| `status_validasi` | `string` | **Ya (Required)** | Status persetujuan validasi (Disetujui, Pending, Ditolak) | `Disetujui` |

### 📤 4. Struktur Response
| Status HTTP | Field / Properti | Tipe Data | Wajib / Required | Keterangan | Contoh Nilai |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **200 OK** | `success` | `boolean` | **Ya (Required)** | Indikator status keberhasilan (`true`) | `true` |
| | `code` | `integer` | **Ya (Required)** | HTTP Status Code (200) | `200` |
| | `message` | `string` | **Ya (Required)** | Pesan informasi respon server | "Operasi berhasil" |
| | `data` | `object` | **Ya (Required)** | Objek data entitas hasil operasi | `{ ... }` |
| | `data.id` | `integer` | **Ya (Required)** | Id | `9` |
| | `data.mahasiswa_id` | `integer` | **Ya (Required)** | ID referensi Mahasiswa | `1` |
| | `data.tahun_akademik_id` | `integer` | **Ya (Required)** | ID referensi Tahun Akademik | `4` |
| | `data.nama_kegiatan` | `string` | **Ya (Required)** | Nama kegiatan kemahasiswaan atau lomba | `"Turnamen Mobile Legends"` |
| | `data.jenis_kegiatan` | `string` | **Ya (Required)** | Jenis kegiatan (Organisasi, Lomba, Seminar, Magang, UKM) | `"UKM"` |
| | `data.peran` | `string` | **Ya (Required)** | Peran atau posisi mahasiswa dalam kegiatan | `"Ketua Tim"` |
| | `data.tingkat` | `string` | **Ya (Required)** | Skala lingkup kegiatan (Jurusan, Polines, Regional, Nasional, Internasional) | `"Polines"` |
| | `data.tanggal_mulai` | `string` | **Ya (Required)** | Tanggal pelaksanaan kegiatan dimulai | `"2024-02-01"` |
| | `data.tanggal_selesai` | `string` | *Tidak (Opsional / Nullable)* | Tanggal pelaksanaan kegiatan selesai | `"2024-06-30"` |
| | `data.penyelenggara` | `string` | **Ya (Required)** | Instansi atau organisasi penyelenggara kegiatan | `"BEM Polines"` |
| | `data.status_validasi` | `string` | **Ya (Required)** | Status persetujuan validasi (Disetujui, Pending, Ditolak) | `"Disetujui"` |
| | `data.created_at` | `string` | **Ya (Required)** | Created At | `"2026-10-19 03:13:35"` |
| | `data.updated_at` | `string` | **Ya (Required)** | Updated At | `"2026-10-19 03:13:35"` |
| **422 Unprocessable** | `success` | `boolean` | **Ya (Required)** | Indikator kegagalan validasi (`false`) | `false` |
| | `code` | `integer` | **Ya (Required)** | HTTP Status Code (422) | `422` |
| | `message` | `string` | **Ya (Required)** | Ringkasan pesan kegagalan validasi | \\"The given data was invalid.\\" |
| | `errors` | `object` | **Ya (Required)** | Kamus pesan kesalahan validasi per field input | `{\\"field\\": [\\"Pesan error\\"]}` |
| **401 Unauthorized** | `message` | `string` | **Ya (Required)** | Pesan otentikasi gagal saat token kosong atau kedaluwarsa | \\"Unauthenticated.\\" |
| **403 Forbidden** | `message` | `string` | **Ya (Required)** | Pesan penolakan izin akses Role/Permission | \\"User does not have the right permissions.\\" |
| **404 Not Found** | `message` | `string` | **Ya (Required)** | Data referensi dengan ID yang diminta tidak ditemukan di database | \\"Data tidak ditemukan\\" |
| **400 Bad Request** | `message` | `string` | **Ya (Required)** | Permintaan tidak valid atau data berelasi masih terikat | \\"Permintaan tidak valid\\" |',
        tags: ['Kegiatan Mahasiswa'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID data id',
                schema: new OA\Schema(type: 'integer', example: 9)
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['mahasiswa_id', 'tahun_akademik_id', 'nama_kegiatan', 'jenis_kegiatan', 'peran', 'tingkat', 'tanggal_mulai', 'penyelenggara', 'status_validasi'],
                properties: [
                    new OA\Property(property: 'mahasiswa_id', type: 'integer', example: 1, description: 'ID referensi Mahasiswa'),
                    new OA\Property(property: 'tahun_akademik_id', type: 'integer', example: 4, description: 'ID referensi Tahun Akademik'),
                    new OA\Property(property: 'nama_kegiatan', type: 'string', example: 'Turnamen Mobile Legends', description: 'Nama kegiatan kemahasiswaan atau lomba'),
                    new OA\Property(property: 'jenis_kegiatan', type: 'string', example: 'UKM', description: 'Jenis kegiatan (Organisasi, Lomba, Seminar, Magang, UKM)'),
                    new OA\Property(property: 'peran', type: 'string', example: 'Ketua Tim', description: 'Peran atau posisi mahasiswa dalam kegiatan'),
                    new OA\Property(property: 'tingkat', type: 'string', example: 'Polines', description: 'Skala lingkup kegiatan (Jurusan, Polines, Regional, Nasional, Internasional)'),
                    new OA\Property(property: 'tanggal_mulai', type: 'string', format: 'date', example: '2024-02-01', description: 'Tanggal pelaksanaan kegiatan dimulai'),
                    new OA\Property(property: 'tanggal_selesai', type: 'string', format: 'date', example: '2024-06-30', description: 'Tanggal pelaksanaan kegiatan selesai', nullable: true),
                    new OA\Property(property: 'penyelenggara', type: 'string', example: 'BEM Polines', description: 'Instansi atau organisasi penyelenggara kegiatan'),
                    new OA\Property(property: 'status_validasi', type: 'string', example: 'Disetujui', description: 'Status persetujuan validasi (Disetujui, Pending, Ditolak)'),
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
        path: '/api/kegiatan-mahasiswa/{id}',
        summary: 'Hapus Data Kegiatan Mahasiswa',
        description: '### 📋 1. Request Headers
| Header | Tipe Data | Wajib / Required | Keterangan | Contoh Nilai |
| :--- | :--- | :--- | :--- | :--- |
| `Authorization` | `string` | **Ya (Required)** | Token Bearer JWT hasil otentikasi login | `Bearer eyJhbGci...` |
| `Accept` | `string` | **Ya (Required)** | Tipe konten respon server yang diharapkan | `application/json` |

### 🔍 2. Path Parameters
| Parameter | Tipe Data | Wajib / Required | Keterangan | Contoh Nilai |
| :--- | :--- | :--- | :--- | :--- |
| `id` | `integer` | **Ya (Required)** | Parameter ID unik id pada path URL | `9` |

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
        tags: ['Kegiatan Mahasiswa'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID data id',
                schema: new OA\Schema(type: 'integer', example: 9)
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
