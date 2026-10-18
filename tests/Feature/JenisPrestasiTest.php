<?php

use App\Models\JenisPrestasi;
use App\Models\Mahasiswa;
use App\Models\PrestasiMahasiswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('unauthenticated user cannot access jenis prestasi endpoints', function () {
    $this->getJson('/api/jenis-prestasi')->assertStatus(401);
});

test('authenticated user can list jenis prestasi', function () {
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

    JenisPrestasi::create([
        'id_prestasi_mahasiswa' => $prestasi->id,
        'jenis' => 'Akademik',
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

    $payload = [
        'id_prestasi_mahasiswa' => $prestasi->id,
        'jenis' => 'Akademik',
    ];

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/jenis-prestasi', $payload);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'message' => 'Data Jenis Prestasi berhasil ditambahkan',
            'data' => [
                'jenis' => 'Akademik',
            ],
        ]);

    $this->assertDatabaseHas('jenis_prestasi', [
        'jenis' => 'Akademik',
    ]);
});

test('authenticated user can view detail of jenis prestasi', function () {
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

    $jenis = JenisPrestasi::create([
        'id_prestasi_mahasiswa' => $prestasi->id,
        'jenis' => 'Akademik',
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson("/api/jenis-prestasi/{$jenis->id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'data' => [
                'id' => $jenis->id,
                'jenis' => 'Akademik',
            ],
        ]);
});

test('authenticated user can update jenis prestasi', function () {
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

    $jenis = JenisPrestasi::create([
        'id_prestasi_mahasiswa' => $prestasi->id,
        'jenis' => 'Jenis Lama',
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->putJson("/api/jenis-prestasi/{$jenis->id}", [
            'id_prestasi_mahasiswa' => $prestasi->id,
            'jenis' => 'Non-Akademik',
        ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Data Jenis Prestasi berhasil diupdate',
        ]);

    $this->assertDatabaseHas('jenis_prestasi', [
        'id' => $jenis->id,
        'jenis' => 'Non-Akademik',
    ]);
});

test('authenticated user can delete jenis prestasi', function () {
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

    $jenis = JenisPrestasi::create([
        'id_prestasi_mahasiswa' => $prestasi->id,
        'jenis' => 'Jenis Hapus',
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
