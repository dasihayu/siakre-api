<?php

use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('unauthenticated user cannot access mahasiswa endpoints', function () {
    $this->getJson('/api/mahasiswa')->assertStatus(401);
});

test('authenticated user can list mahasiswa', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    Mahasiswa::create([
        'nim'               => 33424101,
        'nama_mahasiswa'    => 'Andrian Amirudin',
        'tahun_masuk'       => 2024,
        'jenis_pendaftaran' => 'Reguler',
        'masa_studi_bulan'  => 48,
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/mahasiswa');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Berhasil mengambil data Mahasiswa',
        ]);

    expect($response->json('data'))->toHaveCount(1);
});

test('authenticated user can create mahasiswa', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $payload = [
        'nim'               => 33424102,
        'nama_mahasiswa'    => 'Budi Santoso',
        'tahun_masuk'       => 2024,
        'jenis_pendaftaran' => 'Reguler',
        'tanggal_kelulusan' => '2028-09-13',
        'masa_studi_bulan'  => 48,
    ];

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/mahasiswa', $payload);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'message' => 'Data Mahasiswa berhasil ditambahkan',
        ]);

    $this->assertDatabaseHas('data_mahasiswa', [
        'nim' => 33424102,
    ]);
});

test('system blocks creation of duplicate nim mahasiswa', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    Mahasiswa::create([
        'nim'               => 33424199,
        'nama_mahasiswa'    => 'Mahasiswa Lama',
        'tahun_masuk'       => 2024,
        'jenis_pendaftaran' => 'Reguler',
        'masa_studi_bulan'  => 48,
    ]);

    $payload = [
        'nim'               => 33424199,
        'nama_mahasiswa'    => 'Mahasiswa Baru',
        'tahun_masuk'       => 2024,
        'jenis_pendaftaran' => 'Reguler',
        'masa_studi_bulan'  => 48,
    ];

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/mahasiswa', $payload);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['nim']);
});

test('authenticated user can view detail of mahasiswa', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $mhs = Mahasiswa::create([
        'nim'               => 33424104,
        'nama_mahasiswa'    => 'Siti Aminah',
        'tahun_masuk'       => 2023,
        'jenis_pendaftaran' => 'Transfer',
        'masa_studi_bulan'  => 48,
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson("/api/mahasiswa/{$mhs->id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'data' => [
                'id' => $mhs->id,
                'nim' => 33424104,
            ],
        ]);
});

test('authenticated user can update mahasiswa', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $mhs = Mahasiswa::create([
        'nim'               => 33424105,
        'nama_mahasiswa'    => 'Nama Lama',
        'tahun_masuk'       => 2024,
        'jenis_pendaftaran' => 'Reguler',
        'masa_studi_bulan'  => 48,
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->putJson("/api/mahasiswa/{$mhs->id}", [
            'nim'               => 33424105,
            'nama_mahasiswa'    => 'Nama Baru Update',
            'tahun_masuk'       => 2024,
            'jenis_pendaftaran' => 'Reguler',
            'tanggal_kelulusan' => '2028-10-10',
            'masa_studi_bulan'  => 50,
        ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Data Mahasiswa berhasil diupdate',
        ]);

    $this->assertDatabaseHas('data_mahasiswa', [
        'id' => $mhs->id,
        'nama_mahasiswa' => 'Nama Baru Update',
        'tanggal_kelulusan' => '2028-10-10',
    ]);
});

test('authenticated user can delete mahasiswa', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $mhs = Mahasiswa::create([
        'nim'               => 33424188,
        'nama_mahasiswa'    => 'Akan Dihapus',
        'tahun_masuk'       => 2024,
        'jenis_pendaftaran' => 'Reguler',
        'masa_studi_bulan'  => 48,
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->deleteJson("/api/mahasiswa/{$mhs->id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Data mahasiswa berhasil dihapus',
        ]);

    $this->assertDatabaseMissing('data_mahasiswa', [
        'id' => $mhs->id,
    ]);
});