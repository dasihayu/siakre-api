<?php

use App\Models\JenisPrestasi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('unauthenticated user cannot access jenis prestasi endpoints', function () {
    $this->getJson('/api/jenis-prestasi')->assertStatus(401);
});

test('authenticated user can list jenis prestasi', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    JenisPrestasi::create([
        'kode' => 'JP01',
        'nama' => 'Juara 1 Lomba Pemrograman',
        'kategori' => 'Akademik',
        'tingkat' => 'Nasional',
        'is_active' => true,
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/jenis-prestasi');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Berhasil mengambil data Jenis Prestasi',
        ]);

    expect($response->json('data'))->toHaveCount(1);
});

test('authenticated user can create jenis prestasi', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $payload = [
        'kode' => 'JP02',
        'nama' => 'Juara 2 Hackathon',
        'kategori' => 'Akademik',
        'tingkat' => 'Internasional',
        'is_active' => true,
    ];

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/jenis-prestasi', $payload);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'message' => 'Data Jenis Prestasi berhasil ditambahkan',
            'data' => [
                'kode' => 'JP02',
                'nama' => 'Juara 2 Hackathon',
            ],
        ]);

    $this->assertDatabaseHas('jenis_prestasi', [
        'kode' => 'JP02',
    ]);
});

test('authenticated user can view detail of jenis prestasi', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $jenis = JenisPrestasi::create([
        'kode' => 'JP03',
        'nama' => 'Juara 3 Badminton',
        'kategori' => 'Non-Akademik',
        'tingkat' => 'Regional',
        'is_active' => true,
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson("/api/jenis-prestasi/{$jenis->id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'data' => [
                'id' => $jenis->id,
                'kode' => 'JP03',
            ],
        ]);
});

test('authenticated user can update jenis prestasi', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $jenis = JenisPrestasi::create([
        'kode' => 'JP04',
        'nama' => 'Nama Lama',
        'kategori' => 'Akademik',
        'tingkat' => 'Lokal',
        'is_active' => true,
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->putJson("/api/jenis-prestasi/{$jenis->id}", [
            'kode' => 'JP04-UPD',
            'nama' => 'Nama Baru',
            'kategori' => 'Akademik',
            'tingkat' => 'Nasional',
            'is_active' => true,
        ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Data Jenis Prestasi berhasil diupdate',
        ]);

    $this->assertDatabaseHas('jenis_prestasi', [
        'id' => $jenis->id,
        'kode' => 'JP04-UPD',
        'nama' => 'Nama Baru',
    ]);
});

test('authenticated user can delete jenis prestasi', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $jenis = JenisPrestasi::create([
        'kode' => 'JP05',
        'nama' => 'Akan Dihapus',
        'kategori' => 'Akademik',
        'tingkat' => 'Lokal',
        'is_active' => true,
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->deleteJson("/api/jenis-prestasi/{$jenis->id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Data berhasil dihapus',
        ]);

    $this->assertDatabaseMissing('jenis_prestasi', [
        'id' => $jenis->id,
    ]);
});