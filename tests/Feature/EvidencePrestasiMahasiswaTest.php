<?php

use App\Models\EvidencePrestasiMahasiswa;
use App\Models\Mahasiswa;
use App\Models\PrestasiMahasiswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('unauthenticated user cannot access evidence prestasi mahasiswa endpoints', function () {
    $this->getJson('/api/evidence-prestasi-mahasiswa')->assertStatus(401);
});

test('authenticated user can list evidence prestasi mahasiswa', function () {
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

    EvidencePrestasiMahasiswa::create([
        'id_prestasi_mahasiswa' => $prestasi->id,
        'evidence' => 'Juara 1 OSN',
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/evidence-prestasi-mahasiswa');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Berhasil mengambil data Evidence Prestasi Mahasiswa',
        ]);

    expect($response->json('data'))->toHaveCount(1);
});

test('authenticated user can create evidence prestasi mahasiswa', function () {
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
        'evidence' => 'Juara 1 OSN',
    ];

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/evidence-prestasi-mahasiswa', $payload);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'message' => 'Data Evidence Prestasi Mahasiswa berhasil ditambahkan',
            'data' => [
                'evidence' => 'Juara 1 OSN',
            ],
        ]);

    $this->assertDatabaseHas('evidence_prestasi_mahasiswa', [
        'evidence' => 'Juara 1 OSN',
    ]);
});

test('authenticated user can view detail of evidence prestasi mahasiswa', function () {
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

    $evidence = EvidencePrestasiMahasiswa::create([
        'id_prestasi_mahasiswa' => $prestasi->id,
        'evidence' => 'Juara 1 OSN',
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson("/api/evidence-prestasi-mahasiswa/{$evidence->id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'data' => [
                'id' => $evidence->id,
                'evidence' => 'Juara 1 OSN',
            ],
        ]);
});

test('authenticated user can update evidence prestasi mahasiswa', function () {
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

    $evidence = EvidencePrestasiMahasiswa::create([
        'id_prestasi_mahasiswa' => $prestasi->id,
        'evidence' => 'Evidence Lama',
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->putJson("/api/evidence-prestasi-mahasiswa/{$evidence->id}", [
            'id_prestasi_mahasiswa' => $prestasi->id,
            'evidence' => 'Evidence Baru',
        ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Data Evidence Prestasi Mahasiswa berhasil diupdate',
        ]);

    $this->assertDatabaseHas('evidence_prestasi_mahasiswa', [
        'id' => $evidence->id,
        'evidence' => 'Evidence Baru',
    ]);
});

test('authenticated user can delete evidence prestasi mahasiswa', function () {
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

    $evidence = EvidencePrestasiMahasiswa::create([
        'id_prestasi_mahasiswa' => $prestasi->id,
        'evidence' => 'Evidence Hapus',
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->deleteJson("/api/evidence-prestasi-mahasiswa/{$evidence->id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Data berhasil dihapus',
        ]);

    $this->assertDatabaseMissing('evidence_prestasi_mahasiswa', [
        'id' => $evidence->id,
    ]);
});
