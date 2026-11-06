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
        'url' => 'https://storage.polines.ac.id/sertifikat-osn.pdf',
        'jenis_evidence' => 'Sertifikat',
        'judul' => 'Sertifikat Juara 1 OSN',
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/evidence-prestasi-mahasiswa');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Berhasil mengambil data Evidence Prestasi Mahasiswa',
        ]);

    expect($response->json('data'))->toHaveCount(1);
    expect($response->json('data.0.url'))->toBe('https://storage.polines.ac.id/sertifikat-osn.pdf');
    expect($response->json('data.0.evidence'))->toBe('https://storage.polines.ac.id/sertifikat-osn.pdf');
});

test('authenticated user can create evidence prestasi mahasiswa with url and jenis_evidence', function () {
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
        'url' => 'https://storage.polines.ac.id/sertifikat-osn.pdf',
        'jenis_evidence' => 'Piagam',
        'judul' => 'Piagam Penghargaan OSN',
    ];

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/evidence-prestasi-mahasiswa', $payload);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'message' => 'Data Evidence Prestasi Mahasiswa berhasil ditambahkan',
            'data' => [
                'url' => 'https://storage.polines.ac.id/sertifikat-osn.pdf',
                'jenis_evidence' => 'Piagam',
                'judul' => 'Piagam Penghargaan OSN',
            ],
        ]);

    $this->assertDatabaseHas('evidence_prestasi_mahasiswa', [
        'url' => 'https://storage.polines.ac.id/sertifikat-osn.pdf',
        'jenis_evidence' => 'Piagam',
        'judul' => 'Piagam Penghargaan OSN',
    ]);
});

test('authenticated user can create evidence using legacy evidence field as alias for url', function () {
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
        'evidence' => 'https://storage.polines.ac.id/legacy-url.pdf',
    ];

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/evidence-prestasi-mahasiswa', $payload);

    $response->assertStatus(201);

    $this->assertDatabaseHas('evidence_prestasi_mahasiswa', [
        'url' => 'https://storage.polines.ac.id/legacy-url.pdf',
        'jenis_evidence' => 'Sertifikat',
    ]);
});

test('cannot create evidence with invalid url', function () {
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
        ->postJson('/api/evidence-prestasi-mahasiswa', [
            'id_prestasi_mahasiswa' => $prestasi->id,
            'url' => 'bukan-url-valid',
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['url']);
});

test('cannot create evidence with url exceeding 2048 characters', function () {
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

    $longUrl = 'https://example.com/'.str_repeat('a', 2040);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/evidence-prestasi-mahasiswa', [
            'id_prestasi_mahasiswa' => $prestasi->id,
            'url' => $longUrl,
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['url']);
});

test('cannot create evidence with invalid jenis_evidence', function () {
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
        ->postJson('/api/evidence-prestasi-mahasiswa', [
            'id_prestasi_mahasiswa' => $prestasi->id,
            'url' => 'https://storage.polines.ac.id/dokumen.pdf',
            'jenis_evidence' => 'DokumenPalsu',
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['jenis_evidence']);
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
        'url' => 'https://storage.polines.ac.id/detail.pdf',
        'jenis_evidence' => 'Surat Tugas',
        'judul' => 'Surat Tugas Rektor',
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson("/api/evidence-prestasi-mahasiswa/{$evidence->id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'data' => [
                'id' => $evidence->id,
                'url' => 'https://storage.polines.ac.id/detail.pdf',
                'jenis_evidence' => 'Surat Tugas',
                'judul' => 'Surat Tugas Rektor',
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
        'url' => 'https://storage.polines.ac.id/old.pdf',
        'jenis_evidence' => 'Sertifikat',
        'judul' => 'Old Title',
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->putJson("/api/evidence-prestasi-mahasiswa/{$evidence->id}", [
            'id_prestasi_mahasiswa' => $prestasi->id,
            'url' => 'https://storage.polines.ac.id/new.pdf',
            'jenis_evidence' => 'Publikasi',
            'judul' => 'New Title',
        ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Data Evidence Prestasi Mahasiswa berhasil diupdate',
        ]);

    $this->assertDatabaseHas('evidence_prestasi_mahasiswa', [
        'id' => $evidence->id,
        'url' => 'https://storage.polines.ac.id/new.pdf',
        'jenis_evidence' => 'Publikasi',
        'judul' => 'New Title',
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
        'url' => 'https://storage.polines.ac.id/delete.pdf',
        'jenis_evidence' => 'Lainnya',
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
