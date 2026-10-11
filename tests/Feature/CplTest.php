<?php

use App\Models\Prodi;
use App\Models\Kurikulum;
use App\Models\Cpl;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('unauthenticated user cannot access cpl endpoints', function () {
    $this->getJson('/api/cpl')->assertStatus(401);
});

test('authenticated user can list cpl', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $prodi = Prodi::create(['kode_prodi' => 'PR01', 'nama_prodi' => 'Informatika', 'akreditasi' => 'Unggul']);
    $kurikulum = Kurikulum::create(['id_prodi' => $prodi->id, 'nama_kurikulum' => 'K24', 'berlaku_sampai' => 2028, 'sk_kurikulum' => 'SK01']);
    
    Cpl::create([
        'id_kurikulum'  => $kurikulum->id,
        'kode_cpl'      => 'CPL-01',
        'deskripsi_cpl' => 'Deskripsi CPL Tes',
        'kategori'      => 'Umum'
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/cpl');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Berhasil mengambil data CPL',
        ]);

    expect($response->json('data'))->toHaveCount(1);
});

test('authenticated user can create cpl', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $prodi = Prodi::create(['kode_prodi' => 'PR01', 'nama_prodi' => 'Informatika', 'akreditasi' => 'Unggul']);
    $kurikulum = Kurikulum::create(['id_prodi' => $prodi->id, 'nama_kurikulum' => 'K24', 'berlaku_sampai' => 2028, 'sk_kurikulum' => 'SK01']);

    $payload = [
        'id_kurikulum'  => $kurikulum->id,
        'kode_cpl'      => 'CPL-02',
        'deskripsi_cpl' => 'Mampu menganalisis data',
        'kategori'      => 'Khusus'
    ];

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/cpl', $payload);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'message' => 'Data CPL berhasil ditambahkan',
        ]);

    $this->assertDatabaseHas('cpl', [
        'kode_cpl' => 'CPL-02',
    ]);
});

test('system blocks creation of duplicate kode cpl', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $prodi = Prodi::create(['kode_prodi' => 'PR01', 'nama_prodi' => 'Informatika', 'akreditasi' => 'Unggul']);
    $kurikulum = Kurikulum::create(['id_prodi' => $prodi->id, 'nama_kurikulum' => 'K24', 'berlaku_sampai' => 2028, 'sk_kurikulum' => 'SK01']);
    
    // Bikin CPL pertama
    Cpl::create([
        'id_kurikulum'  => $kurikulum->id,
        'kode_cpl'      => 'CPL-DUPLIKAT',
        'deskripsi_cpl' => 'Deskripsi Awal',
        'kategori'      => 'Umum'
    ]);

    // Coba POST dengan kode yang sama persis
    $payload = [
        'id_kurikulum'  => $kurikulum->id,
        'kode_cpl'      => 'CPL-DUPLIKAT',
        'deskripsi_cpl' => 'Deskripsi Baru',
        'kategori'      => 'Khusus'
    ];

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/cpl', $payload);

    // Harus tertolak oleh validasi (422 Unprocessable Entity)
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['kode_cpl']);
});

test('authenticated user can view detail of cpl', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $prodi = Prodi::create(['kode_prodi' => 'PR01', 'nama_prodi' => 'Informatika', 'akreditasi' => 'Unggul']);
    $kurikulum = Kurikulum::create(['id_prodi' => $prodi->id, 'nama_kurikulum' => 'K24', 'berlaku_sampai' => 2028, 'sk_kurikulum' => 'SK01']);
    
    $cpl = Cpl::create([
        'id_kurikulum'  => $kurikulum->id,
        'kode_cpl'      => 'CPL-03',
        'deskripsi_cpl' => 'Detail CPL',
        'kategori'      => 'Umum'
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson("/api/cpl/{$cpl->id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'data' => [
                'id' => $cpl->id,
                'kode_cpl' => 'CPL-03',
            ],
        ]);
});

test('authenticated user can update cpl', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $prodi = Prodi::create(['kode_prodi' => 'PR01', 'nama_prodi' => 'Informatika', 'akreditasi' => 'Unggul']);
    $kurikulum = Kurikulum::create(['id_prodi' => $prodi->id, 'nama_kurikulum' => 'K24', 'berlaku_sampai' => 2028, 'sk_kurikulum' => 'SK01']);
    
    $cpl = Cpl::create([
        'id_kurikulum'  => $kurikulum->id,
        'kode_cpl'      => 'CPL-04',
        'deskripsi_cpl' => 'Lama',
        'kategori'      => 'Umum'
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->putJson("/api/cpl/{$cpl->id}", [
            'id_kurikulum'  => $kurikulum->id,
            'kode_cpl'      => 'CPL-04-UPDATE',
            'deskripsi_cpl' => 'Baru',
            'kategori'      => 'Khusus'
        ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Data CPL berhasil diupdate',
        ]);

    $this->assertDatabaseHas('cpl', [
        'id' => $cpl->id,
        'kode_cpl' => 'CPL-04-UPDATE',
        'deskripsi_cpl' => 'Baru',
    ]);
});

test('authenticated user can delete cpl', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $prodi = Prodi::create(['kode_prodi' => 'PR01', 'nama_prodi' => 'Informatika', 'akreditasi' => 'Unggul']);
    $kurikulum = Kurikulum::create(['id_prodi' => $prodi->id, 'nama_kurikulum' => 'K24', 'berlaku_sampai' => 2028, 'sk_kurikulum' => 'SK01']);
    
    $cpl = Cpl::create([
        'id_kurikulum'  => $kurikulum->id,
        'kode_cpl'      => 'CPL-HAPUS',
        'deskripsi_cpl' => 'Akan Dihapus',
        'kategori'      => 'Umum'
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->deleteJson("/api/cpl/{$cpl->id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Data berhasil dihapus',
        ]);

    $this->assertDatabaseMissing('cpl', [
        'id' => $cpl->id,
    ]);
});