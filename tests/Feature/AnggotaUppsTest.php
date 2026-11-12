<?php

use App\Models\AnggotaUpps;
use App\Models\PerguruanTinggi;
use App\Models\Upps;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('unauthenticated user cannot access anggota upps endpoints', function () {
    $this->getJson('/api/anggota-upps')->assertStatus(401);
});

test('authenticated user can list anggota upps', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $pt = PerguruanTinggi::create([
        'kode_pt' => 'PT001',
        'nama_pt' => 'Politeknik Negeri Semarang',
        'alamat' => 'Jl. Prof. Sudarto',
        'pimpinan' => 'Dyonisius Beti',
    ]);

    $upps = Upps::create([
        'id_pt' => $pt->id,
        'kode_upps' => 'UPPS01',
        'nama_upps' => 'Fakultas Teknik',
        'jenis' => 'Fakultas', // Mandatory dari migration upps
    ]);

    AnggotaUpps::create([
        'id_upps' => $upps->id,
        'nama' => 'Dr. Siti Rahma, M.T.',
        'jabatan' => 'Wakil Dekan I',
        'urutan_tampil' => 1,
        'periode_mulai' => '2025-01-01',
        'periode_selesai' => '2028-12-31',
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/anggota-upps');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
        ]);

    expect($response->json('data'))->toHaveCount(1);
});

test('authenticated user can create anggota upps', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $pt = PerguruanTinggi::create([
        'kode_pt' => 'PT002',
        'nama_pt' => 'Institut Teknologi',
        'alamat' => 'Jl. Prof. Sudarto',
        'pimpinan' => 'Dyonisius Beti',
    ]);

    $upps = Upps::create([
        'id_pt' => $pt->id,
        'kode_upps' => 'UPPS02',
        'nama_upps' => 'Fakultas Ilmu Komputer',
        'jenis' => 'Fakultas', // Mandatory dari migration upps
    ]);

    $payload = [
        'id_upps' => $upps->id,
        'nama' => 'Prof. Dr. Budi',
        'jabatan' => 'Dekan',
    ];

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/anggota-upps', $payload);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'message' => 'Data Anggota UPPS berhasil ditambahkan',
        ]);

    $this->assertDatabaseHas('anggota_upps', [
        'nama' => 'Prof. Dr. Budi',
    ]);
});

test('authenticated user can view detail, update, and delete anggota upps', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $pt = PerguruanTinggi::create([
        'kode_pt' => 'PT003',
        'nama_pt' => 'Universitas Negeri',
        'alamat' => 'Jl. Prof. Sudarto',
        'pimpinan' => 'Dyonisius Beti',
    ]);

    $upps = Upps::create([
        'id_pt' => $pt->id,
        'kode_upps' => 'UPPS03',
        'nama_upps' => 'Fakultas Kedokteran',
        'jenis' => 'Fakultas', // Mandatory dari migration upps
    ]);

    $anggota = AnggotaUpps::create([
        'id_upps' => $upps->id,
        'nama' => 'Dr. Andi',
        'jabatan' => 'Kaprodi',
    ]);
    $wakil = AnggotaUpps::create([
        'id_upps' => $upps->id,
        'nama' => 'Dr. Budi',
        'jabatan' => 'Wakil Dekan',
        'parent_id' => $anggota->id,
        'urutan_tampil' => 1,
        'periode_mulai' => '2025-01-01',
        'periode_selesai' => '2028-12-31',
    ]);

    // View Detail
    $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson("/api/anggota-upps/{$anggota->id}")
        ->assertStatus(200)
        ->assertJsonPath('data.nama', 'Dr. Andi');

    // Update
    $this->withHeader('Authorization', "Bearer {$token}")
        ->putJson("/api/anggota-upps/{$anggota->id}", [
            'id_upps' => $upps->id,
            'nama' => 'Dr. Andi, Sp.A',
            'jabatan' => 'Wakil Dekan II',
            'urutan_tampil' => 2,
        ])
        ->assertStatus(200);

    $this->assertDatabaseHas('anggota_upps', [
        'nama' => 'Dr. Andi, Sp.A',
        'jabatan' => 'Wakil Dekan II',
        'urutan_tampil' => 2,
    ]);

    expect($wakil->fresh()->parent_id)->toBe($anggota->id);

    // Delete
    $this->withHeader('Authorization', "Bearer {$token}")
        ->deleteJson("/api/anggota-upps/{$anggota->id}")
        ->assertStatus(200);

    $this->assertDatabaseMissing('anggota_upps', [
        'id' => $anggota->id,
    ]);
});
