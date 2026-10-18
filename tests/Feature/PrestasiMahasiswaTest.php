<?php

use App\Models\Mahasiswa;
use App\Models\PrestasiMahasiswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('unauthenticated user cannot access prestasi mahasiswa endpoints', function () {
    $this->getJson('/api/prestasi-mahasiswa')->assertStatus(401);
});

test('authenticated user can list prestasi mahasiswa', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $mahasiswa = Mahasiswa::create([
        'nim' => 33424101,
        'nama_mahasiswa' => 'Andrian Amirudin',
        'tahun_masuk' => 2024,
        'jenis_pendaftaran' => 'Reguler',
        'masa_studi_bulan' => 48,
    ]);

    PrestasiMahasiswa::create([
        'id_mahasiswa' => $mahasiswa->id,
        'nama_aktivitas' => 'Juara 1 OSN',
        'tahun' => 2024,
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/prestasi-mahasiswa');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Berhasil mengambil data Prestasi Mahasiswa',
        ]);

    expect($response->json('data'))->toHaveCount(1);
});

test('authenticated user can create prestasi mahasiswa', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $mahasiswa = Mahasiswa::create([
        'nim' => 33424101,
        'nama_mahasiswa' => 'Andrian Amirudin',
        'tahun_masuk' => 2024,
        'jenis_pendaftaran' => 'Reguler',
        'masa_studi_bulan' => 48,
    ]);

    $payload = [
        'id_mahasiswa' => $mahasiswa->id,
        'nama_aktivitas' => 'Juara 1 OSN',
        'tahun' => 2024,
    ];

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/prestasi-mahasiswa', $payload);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'message' => 'Data Prestasi Mahasiswa berhasil ditambahkan',
            'data' => [
                'nama_aktivitas' => 'Juara 1 OSN',
                'tahun' => 2024,
            ],
        ]);

    $this->assertDatabaseHas('prestasi_mahasiswa', [
        'nama_aktivitas' => 'Juara 1 OSN',
    ]);
});

test('authenticated user can view detail of prestasi mahasiswa', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $mahasiswa = Mahasiswa::create([
        'nim' => 33424101,
        'nama_mahasiswa' => 'Andrian Amirudin',
        'tahun_masuk' => 2024,
        'jenis_pendaftaran' => 'Reguler',
        'masa_studi_bulan' => 48,
    ]);

    $prestasi = PrestasiMahasiswa::create([
        'id_mahasiswa' => $mahasiswa->id,
        'nama_aktivitas' => 'Juara 1 OSN',
        'tahun' => 2024,
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson("/api/prestasi-mahasiswa/{$prestasi->id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'data' => [
                'id' => $prestasi->id,
                'nama_aktivitas' => 'Juara 1 OSN',
            ],
        ]);
});

test('authenticated user can update prestasi mahasiswa', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $mahasiswa = Mahasiswa::create([
        'nim' => 33424101,
        'nama_mahasiswa' => 'Andrian Amirudin',
        'tahun_masuk' => 2024,
        'jenis_pendaftaran' => 'Reguler',
        'masa_studi_bulan' => 48,
    ]);

    $prestasi = PrestasiMahasiswa::create([
        'id_mahasiswa' => $mahasiswa->id,
        'nama_aktivitas' => 'Juara Lama',
        'tahun' => 2023,
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->putJson("/api/prestasi-mahasiswa/{$prestasi->id}", [
            'id_mahasiswa' => $mahasiswa->id,
            'nama_aktivitas' => 'Juara Baru',
            'tahun' => 2024,
        ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Data Prestasi Mahasiswa berhasil diupdate',
        ]);

    $this->assertDatabaseHas('prestasi_mahasiswa', [
        'id' => $prestasi->id,
        'nama_aktivitas' => 'Juara Baru',
    ]);
});

test('authenticated user can delete prestasi mahasiswa', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $mahasiswa = Mahasiswa::create([
        'nim' => 33424101,
        'nama_mahasiswa' => 'Andrian Amirudin',
        'tahun_masuk' => 2024,
        'jenis_pendaftaran' => 'Reguler',
        'masa_studi_bulan' => 48,
    ]);

    $prestasi = PrestasiMahasiswa::create([
        'id_mahasiswa' => $mahasiswa->id,
        'nama_aktivitas' => 'Prestasi Hapus',
        'tahun' => 2024,
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->deleteJson("/api/prestasi-mahasiswa/{$prestasi->id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Data berhasil dihapus',
        ]);

    $this->assertDatabaseMissing('prestasi_mahasiswa', [
        'id' => $prestasi->id,
    ]);
});
