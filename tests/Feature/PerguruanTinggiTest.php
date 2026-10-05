<?php

use App\Models\PerguruanTinggi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('unauthenticated user cannot access perguruan tinggi endpoints', function () {
    $this->getJson('/api/perguruan-tinggi')->assertStatus(401);
});

test('authenticated user can list perguruan tinggi', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    PerguruanTinggi::create([
        'kode_pt' => 'PT001',
        'nama_pt' => 'Politeknik Negeri Semarang',
        'alamat' => 'Jl. Prof. Sudarto',
        'pimpinan' => 'Dyonisius Beti',
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/perguruan-tinggi');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Berhasil mengambil data',
        ]);

    expect($response->json('data'))->toHaveCount(1);
});

test('authenticated user can create perguruan tinggi', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $payload = [
        'kode_pt' => 'PT002',
        'nama_pt' => 'Universitas Gadjah Mada',
        'alamat' => 'Yogyakarta',
        'pimpinan' => 'Prof. Ova Emilia',
    ];

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/perguruan-tinggi', $payload);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'message' => 'Data Perguruan Tinggi berhasil ditambahkan',
            'data' => [
                'kode_pt' => 'PT002',
                'nama_pt' => 'Universitas Gadjah Mada',
            ],
        ]);

    $this->assertDatabaseHas('perguruan_tinggis', [
        'kode_pt' => 'PT002',
        'nama_pt' => 'Universitas Gadjah Mada',
    ]);
});

test('authenticated user can view detail of perguruan tinggi', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $pt = PerguruanTinggi::create([
        'kode_pt' => 'PT001',
        'nama_pt' => 'Polines',
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson("/api/perguruan-tinggi/{$pt->id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'data' => [
                'id' => $pt->id,
                'kode_pt' => 'PT001',
            ],
        ]);
});

test('authenticated user can update perguruan tinggi', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $pt = PerguruanTinggi::create([
        'kode_pt' => 'PT001',
        'nama_pt' => 'Polines Lama',
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->putJson("/api/perguruan-tinggi/{$pt->id}", [
            'kode_pt' => 'PT001',
            'nama_pt' => 'Polines Baru',
        ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Data berhasil diupdate',
        ]);

    $this->assertDatabaseHas('perguruan_tinggis', [
        'id' => $pt->id,
        'nama_pt' => 'Polines Baru',
    ]);
});

test('authenticated user can delete perguruan tinggi when not linked to upps', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $pt = PerguruanTinggi::create([
        'kode_pt' => 'PT999',
        'nama_pt' => 'PT Hapus',
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->deleteJson("/api/perguruan-tinggi/{$pt->id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Data berhasil dihapus',
        ]);

    $this->assertDatabaseMissing('perguruan_tinggis', [
        'id' => $pt->id,
    ]);
});
