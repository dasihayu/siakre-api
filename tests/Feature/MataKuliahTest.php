<?php

use App\Models\Prodi;
use App\Models\Kurikulum;
use App\Models\MataKuliah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('unauthenticated user cannot access mata kuliah endpoints', function () {
    $this->getJson('/api/mata-kuliah')->assertStatus(401);
});

test('authenticated user can list mata kuliah', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $prodi = Prodi::create(['kode_prodi' => 'PR01', 'nama_prodi' => 'Informatika', 'akreditasi' => 'Unggul']);
    $kurikulum = Kurikulum::create(['id_prodi' => $prodi->id, 'nama_kurikulum' => 'K24', 'berlaku_sampai' => 2028, 'sk_kurikulum' => 'SK01']);
    
    MataKuliah::create([
        'id_kurikulum'     => $kurikulum->id,
        'kode_mata_kuliah' => 'IF101',
        'nama_mata_kuliah' => 'Pemrograman Web',
        'sks'              => 3,
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/mata-kuliah');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Berhasil mengambil data Mata Kuliah',
        ]);

    expect($response->json('data'))->toHaveCount(1);
});

test('authenticated user can create mata kuliah', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $prodi = Prodi::create(['kode_prodi' => 'PR01', 'nama_prodi' => 'Informatika', 'akreditasi' => 'Unggul']);
    $kurikulum = Kurikulum::create(['id_prodi' => $prodi->id, 'nama_kurikulum' => 'K24', 'berlaku_sampai' => 2028, 'sk_kurikulum' => 'SK01']);

    $payload = [
        'id_kurikulum'     => $kurikulum->id,
        'kode_mata_kuliah' => 'IF102',
        'nama_mata_kuliah' => 'Basis Data',
        'sks'              => 4,
    ];

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/mata-kuliah', $payload);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'message' => 'Data Mata Kuliah berhasil ditambahkan',
        ]);

    $this->assertDatabaseHas('mata_kuliah', [
        'kode_mata_kuliah' => 'IF102',
    ]);
});

test('system blocks creation of duplicate kode mata kuliah', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $prodi = Prodi::create(['kode_prodi' => 'PR01', 'nama_prodi' => 'Informatika', 'akreditasi' => 'Unggul']);
    $kurikulum = Kurikulum::create(['id_prodi' => $prodi->id, 'nama_kurikulum' => 'K24', 'berlaku_sampai' => 2028, 'sk_kurikulum' => 'SK01']);
    
    MataKuliah::create([
        'id_kurikulum'     => $kurikulum->id,
        'kode_mata_kuliah' => 'IF-SAMA',
        'nama_mata_kuliah' => 'Matkul Awal',
        'sks'              => 3,
    ]);

    $payload = [
        'id_kurikulum'     => $kurikulum->id,
        'kode_mata_kuliah' => 'IF-SAMA',
        'nama_mata_kuliah' => 'Matkul Duplikat',
        'sks'              => 3,
    ];

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/mata-kuliah', $payload);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['kode_mata_kuliah']);
});

test('authenticated user can view detail of mata kuliah', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $prodi = Prodi::create(['kode_prodi' => 'PR01', 'nama_prodi' => 'Informatika', 'akreditasi' => 'Unggul']);
    $kurikulum = Kurikulum::create(['id_prodi' => $prodi->id, 'nama_kurikulum' => 'K24', 'berlaku_sampai' => 2028, 'sk_kurikulum' => 'SK01']);
    
    $matkul = MataKuliah::create([
        'id_kurikulum'     => $kurikulum->id,
        'kode_mata_kuliah' => 'IF103',
        'nama_mata_kuliah' => 'Jaringan Komputer',
        'sks'              => 3,
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson("/api/mata-kuliah/{$matkul->id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'data' => [
                'id' => $matkul->id,
                'kode_mata_kuliah' => 'IF103',
            ],
        ]);
});

test('authenticated user can update mata kuliah', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $prodi = Prodi::create(['kode_prodi' => 'PR01', 'nama_prodi' => 'Informatika', 'akreditasi' => 'Unggul']);
    $kurikulum = Kurikulum::create(['id_prodi' => $prodi->id, 'nama_kurikulum' => 'K24', 'berlaku_sampai' => 2028, 'sk_kurikulum' => 'SK01']);
    
    $matkul = MataKuliah::create([
        'id_kurikulum'     => $kurikulum->id,
        'kode_mata_kuliah' => 'IF104',
        'nama_mata_kuliah' => 'Nama Lama',
        'sks'              => 2,
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->putJson("/api/mata-kuliah/{$matkul->id}", [
            'id_kurikulum'     => $kurikulum->id,
            'kode_mata_kuliah' => 'IF104-BARU',
            'nama_mata_kuliah' => 'Nama Baru',
            'sks'              => 3,
        ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Data Mata Kuliah berhasil diupdate',
        ]);

    $this->assertDatabaseHas('mata_kuliah', [
        'id' => $matkul->id,
        'kode_mata_kuliah' => 'IF104-BARU',
        'nama_mata_kuliah' => 'Nama Baru',
    ]);
});

test('authenticated user can delete mata kuliah', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $prodi = Prodi::create(['kode_prodi' => 'PR01', 'nama_prodi' => 'Informatika', 'akreditasi' => 'Unggul']);
    $kurikulum = Kurikulum::create(['id_prodi' => $prodi->id, 'nama_kurikulum' => 'K24', 'berlaku_sampai' => 2028, 'sk_kurikulum' => 'SK01']);
    
    $matkul = MataKuliah::create([
        'id_kurikulum'     => $kurikulum->id,
        'kode_mata_kuliah' => 'IF999',
        'nama_mata_kuliah' => 'Matkul Hapus',
        'sks'              => 2,
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->deleteJson("/api/mata-kuliah/{$matkul->id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Data berhasil dihapus',
        ]);

    $this->assertDatabaseMissing('mata_kuliah', [
        'id' => $matkul->id,
    ]);
});