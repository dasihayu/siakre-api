<?php

use App\Models\Prodi;
use App\Models\Kurikulum;
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
        'id_prodi'       => $prodi->id,
        'nama_kurikulum' => 'Kurikulum 2024',
        'berlaku_sampai' => 2028,
        'sk_kurikulum'   => 'SK/2024/001',
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
        'id_prodi'       => $prodi->id,
        'nama_kurikulum' => 'Kurikulum Merdeka 2024',
        'berlaku_sampai' => 2028,
        'sk_kurikulum'   => 'SK/2024/001',
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
        'id_prodi'       => $prodi->id,
        'nama_kurikulum' => 'Kurikulum 2024',
        'berlaku_sampai' => 2028,
        'sk_kurikulum'   => 'SK/2024/001',
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
        'id_prodi'       => $prodi->id,
        'nama_kurikulum' => 'Kurikulum Lama',
        'berlaku_sampai' => 2025,
        'sk_kurikulum'   => 'SK/2020/001',
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->putJson("/api/kurikulum/{$kurikulum->id}", [
            'id_prodi'       => $prodi->id,
            'nama_kurikulum' => 'Kurikulum Baru',
            'berlaku_sampai' => 2030,
            'sk_kurikulum'   => 'SK/2025/001',
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
        'id_prodi'       => $prodi->id,
        'nama_kurikulum' => 'Kurikulum Hapus',
        'berlaku_sampai' => 2028,
        'sk_kurikulum'   => 'SK/2024/001',
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