<?php

use App\Models\PerguruanTinggi;
use App\Models\Prodi;
use App\Models\Upps;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('unauthenticated user cannot access upps endpoints', function () {
    $this->getJson('/api/upps')->assertStatus(401);
});

test('authenticated user can list upps', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $pt = PerguruanTinggi::create(['kode_pt' => 'PT001', 'nama_pt' => 'Polines']);

    Upps::create([
        'id_pt' => $pt->id,
        'kode_upps' => 'UPPS01',
        'nama_upps' => 'Jurusan Teknik Elektro',
        'pimpinan_upps' => 'Joko Susilo',
        'jenis' => 'Jurusan',
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/upps');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Berhasil mengambil data UPPS',
        ]);

    expect($response->json('data'))->toHaveCount(1);
});

test('authenticated user can create upps', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $pt = PerguruanTinggi::create(['kode_pt' => 'PT001', 'nama_pt' => 'Polines']);

    $payload = [
        'id_pt' => $pt->id,
        'kode_upps' => 'UPPS02',
        'nama_upps' => 'Jurusan Teknik Mesin',
        'pimpinan_upps' => 'Ahmad Yani',
        'jenis' => 'Jurusan',
    ];

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/upps', $payload);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'message' => 'Data UPPS berhasil ditambahkan',
            'data' => [
                'kode_upps' => 'UPPS02',
                'nama_upps' => 'Jurusan Teknik Mesin',
            ],
        ]);

    $this->assertDatabaseHas('upps', [
        'kode_upps' => 'UPPS02',
    ]);
});

test('authenticated user can view detail of upps', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $pt = PerguruanTinggi::create(['kode_pt' => 'PT001', 'nama_pt' => 'Polines']);

    $upps = Upps::create([
        'id_pt' => $pt->id,
        'kode_upps' => 'UPPS03',
        'nama_upps' => 'Fakultas Teknik',
        'jenis' => 'Fakultas',
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson("/api/upps/{$upps->id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'data' => [
                'id' => $upps->id,
                'kode_upps' => 'UPPS03',
            ],
        ]);
});

test('authenticated user can update upps', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $pt = PerguruanTinggi::create(['kode_pt' => 'PT001', 'nama_pt' => 'Polines']);

    $upps = Upps::create([
        'id_pt' => $pt->id,
        'kode_upps' => 'UPPS04',
        'nama_upps' => 'Nama Lama UPPS',
        'jenis' => 'Jurusan',
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->putJson("/api/upps/{$upps->id}", [
            'id_pt' => $pt->id,
            'kode_upps' => 'UPPS04',
            'nama_upps' => 'Nama Baru UPPS',
            'jenis' => 'Fakultas',
        ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Data UPPS berhasil diupdate',
        ]);

    $this->assertDatabaseHas('upps', [
        'id' => $upps->id,
        'nama_upps' => 'Nama Baru UPPS',
    ]);
});

test('authenticated user can delete upps', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $pt = PerguruanTinggi::create(['kode_pt' => 'PT001', 'nama_pt' => 'Polines']);

    $upps = Upps::create([
        'id_pt' => $pt->id,
        'kode_upps' => 'UPPS99',
        'nama_upps' => 'UPPS Hapus',
        'jenis' => 'Jurusan',
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->deleteJson("/api/upps/{$upps->id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Data berhasil dihapus',
        ]);

    $this->assertDatabaseMissing('upps', [
        'id' => $upps->id,
    ]);
});

test('upps cannot be deleted while it has study programs', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);
    $pt = PerguruanTinggi::create(['kode_pt' => 'PT-PRODI', 'nama_pt' => 'PT Prodi']);
    $upps = Upps::create([
        'id_pt' => $pt->id,
        'kode_upps' => 'UPPS-PRODI',
        'nama_upps' => 'UPPS Prodi',
        'jenis' => 'Fakultas',
    ]);
    Prodi::create([
        'id_upps' => $upps->id,
        'kode_prodi' => 'PR-PRODI',
        'nama_prodi' => 'Prodi Terikat',
    ]);

    $this->withHeader('Authorization', '******')
        ->deleteJson("/api/upps/{$upps->id}")
        ->assertStatus(400)
        ->assertJsonPath('message', 'UPPS masih memiliki program studi');
});
