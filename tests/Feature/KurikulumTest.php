<?php

use App\Models\Kurikulum;
use App\Models\MataKuliah;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('unauthenticated user cannot access kurikulum endpoints', function () {
    $this->getJson('/api/kurikulum')->assertStatus(401);
});

test('authenticated user can list kurikulum', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $prodi = Prodi::create(['kode_prodi' => 'PR01', 'nama_prodi' => 'Informatika', 'akreditasi' => 'Unggul']);

    Kurikulum::create([
        'id_prodi' => $prodi->id,
        'nama_kurikulum' => 'Kurikulum 2024',
        'berlaku_sampai' => 2028,
        'sk_kurikulum' => 'SK/2024/001',
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/kurikulum');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Berhasil mengambil data Kurikulum',
        ]);

    expect($response->json('data'))->toHaveCount(1);
});

test('authenticated user can create kurikulum', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $prodi = Prodi::create(['kode_prodi' => 'PR01', 'nama_prodi' => 'Informatika', 'akreditasi' => 'Unggul']);

    $payload = [
        'id_prodi' => $prodi->id,
        'nama_kurikulum' => 'Kurikulum Merdeka 2024',
        'berlaku_sampai' => 2028,
        'sk_kurikulum' => 'SK/2024/001',
    ];

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/kurikulum', $payload);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'message' => 'Data Kurikulum berhasil ditambahkan',
            'data' => [
                'nama_kurikulum' => 'Kurikulum Merdeka 2024',
            ],
        ]);

    $this->assertDatabaseHas('kurikulum', [
        'nama_kurikulum' => 'Kurikulum Merdeka 2024',
    ]);
});

test('authenticated user can view detail of kurikulum', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $prodi = Prodi::create(['kode_prodi' => 'PR01', 'nama_prodi' => 'Informatika', 'akreditasi' => 'Unggul']);

    $kurikulum = Kurikulum::create([
        'id_prodi' => $prodi->id,
        'nama_kurikulum' => 'Kurikulum 2024',
        'berlaku_sampai' => 2028,
        'sk_kurikulum' => 'SK/2024/001',
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson("/api/kurikulum/{$kurikulum->id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'data' => [
                'id' => $kurikulum->id,
                'nama_kurikulum' => 'Kurikulum 2024',
            ],
        ]);
});

test('authenticated user can update kurikulum', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $prodi = Prodi::create(['kode_prodi' => 'PR01', 'nama_prodi' => 'Informatika', 'akreditasi' => 'Unggul']);

    $kurikulum = Kurikulum::create([
        'id_prodi' => $prodi->id,
        'nama_kurikulum' => 'Kurikulum Lama',
        'berlaku_sampai' => 2025,
        'sk_kurikulum' => 'SK/2020/001',
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->putJson("/api/kurikulum/{$kurikulum->id}", [
            'id_prodi' => $prodi->id,
            'nama_kurikulum' => 'Kurikulum Baru',
            'berlaku_sampai' => 2030,
            'sk_kurikulum' => 'SK/2025/001',
        ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Data Kurikulum berhasil diupdate',
        ]);

    $this->assertDatabaseHas('kurikulum', [
        'id' => $kurikulum->id,
        'nama_kurikulum' => 'Kurikulum Baru',
    ]);
});

test('authenticated user can delete kurikulum', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $prodi = Prodi::create(['kode_prodi' => 'PR01', 'nama_prodi' => 'Informatika', 'akreditasi' => 'Unggul']);

    $kurikulum = Kurikulum::create([
        'id_prodi' => $prodi->id,
        'nama_kurikulum' => 'Kurikulum Hapus',
        'berlaku_sampai' => 2028,
        'sk_kurikulum' => 'SK/2024/001',
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->deleteJson("/api/kurikulum/{$kurikulum->id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Data berhasil dihapus',
        ]);

    $this->assertDatabaseMissing('kurikulum', [
        'id' => $kurikulum->id,
    ]);
});

test('authenticated user can get mata kuliah per kurikulum', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $prodi = Prodi::create(['kode_prodi' => 'PR01', 'nama_prodi' => 'Informatika', 'akreditasi' => 'Unggul']);

    $kurikulum = Kurikulum::create([
        'id_prodi' => $prodi->id,
        'nama_kurikulum' => 'Kurikulum 2024',
        'berlaku_sampai' => 2028,
        'sk_kurikulum' => 'SK/2024/001',
    ]);

    $kurikulumLain = Kurikulum::create([
        'id_prodi' => $prodi->id,
        'nama_kurikulum' => 'Kurikulum 2020',
        'berlaku_sampai' => 2024,
        'sk_kurikulum' => 'SK/2020/001',
    ]);

    MataKuliah::create([
        'id_kurikulum' => $kurikulum->id,
        'kode_mata_kuliah' => 'MK01',
        'nama_mata_kuliah' => 'Pemrograman Web',
        'sks' => 3,
    ]);

    MataKuliah::create([
        'id_kurikulum' => $kurikulum->id,
        'kode_mata_kuliah' => 'MK02',
        'nama_mata_kuliah' => 'Basis Data',
        'sks' => 3,
    ]);

    MataKuliah::create([
        'id_kurikulum' => $kurikulumLain->id,
        'kode_mata_kuliah' => 'MK03',
        'nama_mata_kuliah' => 'Struktur Data Lama',
        'sks' => 2,
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson("/api/kurikulum/{$kurikulum->id}/mata-kuliah");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Berhasil mengambil data Mata Kuliah per Kurikulum',
        ]);

    expect($response->json('data'))->toHaveCount(2);

    $response404 = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/kurikulum/99999/mata-kuliah');
    $response404->assertStatus(404);
});

test('authenticated user can create kurikulum with new fields', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $prodi = Prodi::create(['kode_prodi' => 'PR02', 'nama_prodi' => 'Teknik Komputer', 'akreditasi' => 'Unggul']);

    $payload = [
        'id_prodi' => $prodi->id,
        'nama_kurikulum' => 'Kurikulum OBE 2026',
        'tahun_mulai' => 2026,
        'berlaku_sampai' => 2030,
        'status' => 'Aktif',
        'dokumen_url' => 'https://polines.ac.id/kurikulum-obe-2026.pdf',
        'profil_lulusan' => 'IoT Specialist, System Integrator',
        'sk_kurikulum' => 'SK/2026/009',
    ];

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/kurikulum', $payload);

    $response->assertStatus(201)
        ->assertJsonPath('data.tahun_mulai', 2026)
        ->assertJsonPath('data.status', 'Aktif')
        ->assertJsonPath('data.dokumen_url', 'https://polines.ac.id/kurikulum-obe-2026.pdf')
        ->assertJsonPath('data.profil_lulusan', 'IoT Specialist, System Integrator');

    $this->assertDatabaseHas('kurikulum', [
        'nama_kurikulum' => 'Kurikulum OBE 2026',
        'tahun_mulai' => 2026,
        'status' => 'Aktif',
        'dokumen_url' => 'https://polines.ac.id/kurikulum-obe-2026.pdf',
        'profil_lulusan' => 'IoT Specialist, System Integrator',
    ]);
});

test('cannot create more than one active kurikulum for the same prodi', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $prodi = Prodi::create(['kode_prodi' => 'PR03', 'nama_prodi' => 'Sistem Informasi', 'akreditasi' => 'Baik']);

    // Kurikulum aktif pertama
    Kurikulum::create([
        'id_prodi' => $prodi->id,
        'nama_kurikulum' => 'Kurikulum Aktif 1',
        'tahun_mulai' => 2022,
        'berlaku_sampai' => 2026,
        'status' => 'Aktif',
    ]);

    // Percobaan membuat kurikulum aktif kedua untuk prodi yang sama
    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/kurikulum', [
            'id_prodi' => $prodi->id,
            'nama_kurikulum' => 'Kurikulum Aktif 2',
            'tahun_mulai' => 2024,
            'berlaku_sampai' => 2028,
            'status' => 'Aktif',
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['status']);
});

test('can create archive kurikulum when active kurikulum already exists in prodi', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $prodi = Prodi::create(['kode_prodi' => 'PR04', 'nama_prodi' => 'Teknik Mesin', 'akreditasi' => 'Baik']);

    Kurikulum::create([
        'id_prodi' => $prodi->id,
        'nama_kurikulum' => 'Kurikulum Aktif Saat Ini',
        'tahun_mulai' => 2022,
        'berlaku_sampai' => 2026,
        'status' => 'Aktif',
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/kurikulum', [
            'id_prodi' => $prodi->id,
            'nama_kurikulum' => 'Kurikulum Lama Yang Diarsipkan',
            'tahun_mulai' => 2018,
            'berlaku_sampai' => 2022,
            'status' => 'Arsip',
        ]);

    $response->assertStatus(201)
        ->assertJsonPath('data.status', 'Arsip');
});

test('cannot update an archived kurikulum to active if another active kurikulum exists in same prodi', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $prodi = Prodi::create(['kode_prodi' => 'PR05', 'nama_prodi' => 'Teknik Sipil', 'akreditasi' => 'Baik']);

    Kurikulum::create([
        'id_prodi' => $prodi->id,
        'nama_kurikulum' => 'Kurikulum Sedang Aktif',
        'tahun_mulai' => 2024,
        'berlaku_sampai' => 2028,
        'status' => 'Aktif',
    ]);

    $kurikulumArsip = Kurikulum::create([
        'id_prodi' => $prodi->id,
        'nama_kurikulum' => 'Kurikulum Versi Lama',
        'tahun_mulai' => 2020,
        'berlaku_sampai' => 2024,
        'status' => 'Arsip',
    ]);

    // Coba aktifkan kembali kurikulum lama
    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->putJson("/api/kurikulum/{$kurikulumArsip->id}", [
            'id_prodi' => $prodi->id,
            'nama_kurikulum' => 'Kurikulum Versi Lama Mau Diaktifkan',
            'berlaku_sampai' => 2024,
            'status' => 'Aktif',
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['status']);
});

test('can update active kurikulum attributes without triggering unique active validation', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $prodi = Prodi::create(['kode_prodi' => 'PR06', 'nama_prodi' => 'Akuntansi', 'akreditasi' => 'Unggul']);

    $kurikulum = Kurikulum::create([
        'id_prodi' => $prodi->id,
        'nama_kurikulum' => 'Kurikulum Awal',
        'tahun_mulai' => 2023,
        'berlaku_sampai' => 2027,
        'status' => 'Aktif',
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->putJson("/api/kurikulum/{$kurikulum->id}", [
            'id_prodi' => $prodi->id,
            'nama_kurikulum' => 'Kurikulum Awal Diperbarui',
            'tahun_mulai' => 2023,
            'berlaku_sampai' => 2028,
            'status' => 'Aktif',
            'dokumen_url' => 'https://polines.ac.id/kurikulum-update.pdf',
            'profil_lulusan' => 'Akuntan Publik, Auditor',
        ]);

    $response->assertStatus(200)
        ->assertJsonPath('data.nama_kurikulum', 'Kurikulum Awal Diperbarui')
        ->assertJsonPath('data.status', 'Aktif')
        ->assertJsonPath('data.profil_lulusan', 'Akuntan Publik, Auditor');
});
