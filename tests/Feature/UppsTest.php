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
    $prodi = Prodi::create(['kode_prodi' => 'PR01', 'nama_prodi' => 'Informatika', 'akreditasi' => 'A']);

    Upps::create([
        'id_pt' => $pt->id,
        'kode_upps' => 'UPPS01',
        'nama_upps' => 'Jurusan Teknik Elektro',
        'pimpinan_upps' => 'Joko Susilo',
        'prodi_id' => $prodi->id,
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
    $prodi = Prodi::create(['kode_prodi' => 'PR01', 'nama_prodi' => 'Informatika', 'akreditasi' => 'A']);

    $payload = [
        'id_pt' => $pt->id,
        'kode_upps' => 'UPPS02',
        'nama_upps' => 'Jurusan Teknik Mesin',
        'pimpinan_upps' => 'Ahmad Yani',
        'prodi_id' => $prodi->id,
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
    $prodi = Prodi::create(['kode_prodi' => 'PR01', 'nama_prodi' => 'Informatika', 'akreditasi' => 'A']);

    $upps = Upps::create([
        'id_pt' => $pt->id,
        'kode_upps' => 'UPPS03',
        'nama_upps' => 'Fakultas Teknik',
        'prodi_id' => $prodi->id,
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
    $prodi = Prodi::create(['kode_prodi' => 'PR01', 'nama_prodi' => 'Informatika', 'akreditasi' => 'A']);

    $upps = Upps::create([
        'id_pt' => $pt->id,
        'kode_upps' => 'UPPS04',
        'nama_upps' => 'Nama Lama UPPS',
        'prodi_id' => $prodi->id,
        'jenis' => 'Jurusan',
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->putJson("/api/upps/{$upps->id}", [
            'id_pt' => $pt->id,
            'kode_upps' => 'UPPS04',
            'nama_upps' => 'Nama Baru UPPS',
            'prodi_id' => $prodi->id,
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
    $prodi = Prodi::create(['kode_prodi' => 'PR01', 'nama_prodi' => 'Informatika', 'akreditasi' => 'A']);

    $upps = Upps::create([
        'id_pt' => $pt->id,
        'kode_upps' => 'UPPS99',
        'nama_upps' => 'UPPS Hapus',
        'prodi_id' => $prodi->id,
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
