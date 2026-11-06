<?php

use App\Enums\JenisEvidencePrestasi;
use App\Models\EvidencePrestasiMahasiswa;
use App\Models\Mahasiswa;
use App\Models\PrestasiMahasiswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('unauthenticated user cannot access prestasi mahasiswa endpoints', function () {
    $this->getJson('/api/prestasi-mahasiswa')->assertStatus(401);
});

test('authenticated user can list prestasi mahasiswa with evidence relations', function () {
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
        'jenis_evidence' => JenisEvidencePrestasi::SERTIFIKAT->value,
        'judul' => 'Sertifikat Juara 1 OSN',
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/prestasi-mahasiswa');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Berhasil mengambil data Prestasi Mahasiswa',
        ]);

    expect($response->json('data'))->toHaveCount(1);
    expect($response->json('data.0.evidence'))->toHaveCount(1);
    expect($response->json('data.0.evidence.0.url'))->toBe('https://storage.polines.ac.id/sertifikat-osn.pdf');
});

test('authenticated user can create prestasi mahasiswa with batch evidence', function () {
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
        'evidence' => [
            [
                'url' => 'https://storage.polines.ac.id/sertifikat-osn.pdf',
                'jenis_evidence' => 'Sertifikat',
                'judul' => 'Sertifikat Juara 1 OSN',
            ],
            [
                'url' => 'https://kemahasiswaan.polines.ac.id/foto-osn.jpg',
                'jenis_evidence' => 'Foto Kegiatan',
                'judul' => 'Dokumentasi Penyerahan Medali',
            ],
        ],
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

    $this->assertDatabaseHas('evidence_prestasi_mahasiswa', [
        'url' => 'https://storage.polines.ac.id/sertifikat-osn.pdf',
        'jenis_evidence' => 'Sertifikat',
        'judul' => 'Sertifikat Juara 1 OSN',
    ]);

    $this->assertDatabaseHas('evidence_prestasi_mahasiswa', [
        'url' => 'https://kemahasiswaan.polines.ac.id/foto-osn.jpg',
        'jenis_evidence' => 'Foto Kegiatan',
        'judul' => 'Dokumentasi Penyerahan Medali',
    ]);

    expect($response->json('data.evidence'))->toHaveCount(2);
});

test('authenticated user can create prestasi mahasiswa with simple string urls in evidence', function () {
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
        'nama_aktivitas' => 'Finalis Lomba Coding',
        'tahun' => 2025,
        'evidence' => [
            'https://storage.polines.ac.id/bukti-coding.pdf',
        ],
    ];

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/prestasi-mahasiswa', $payload);

    $response->assertStatus(201);

    $this->assertDatabaseHas('evidence_prestasi_mahasiswa', [
        'url' => 'https://storage.polines.ac.id/bukti-coding.pdf',
        'jenis_evidence' => 'Sertifikat',
    ]);
});

test('fails to create prestasi mahasiswa with invalid url in evidence', function () {
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
        'evidence' => [
            'bukan-tautan-valid',
        ],
    ];

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/prestasi-mahasiswa', $payload);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['evidence.0.url']);
});

test('fails to create prestasi mahasiswa with invalid jenis_evidence in evidence', function () {
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
        'evidence' => [
            [
                'url' => 'https://example.com/sertifikat.pdf',
                'jenis_evidence' => 'TipeNgawur',
            ],
        ],
    ];

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/prestasi-mahasiswa', $payload);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['evidence.0.jenis_evidence']);
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

    EvidencePrestasiMahasiswa::create([
        'id_prestasi_mahasiswa' => $prestasi->id,
        'url' => 'https://storage.polines.ac.id/detail.pdf',
        'jenis_evidence' => 'Piagam',
        'judul' => 'Piagam Penghargaan',
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

    expect($response->json('data.evidence'))->toHaveCount(1);
    expect($response->json('data.evidence.0.jenis_evidence'))->toBe('Piagam');
});

test('authenticated user can update prestasi mahasiswa and sync batch evidence', function () {
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

    $existingEvidence1 = EvidencePrestasiMahasiswa::create([
        'id_prestasi_mahasiswa' => $prestasi->id,
        'url' => 'https://storage.polines.ac.id/old-1.pdf',
        'jenis_evidence' => 'Sertifikat',
        'judul' => 'Old 1',
    ]);

    $existingEvidence2 = EvidencePrestasiMahasiswa::create([
        'id_prestasi_mahasiswa' => $prestasi->id,
        'url' => 'https://storage.polines.ac.id/old-2.pdf',
        'jenis_evidence' => 'Sertifikat',
        'judul' => 'Old 2',
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->putJson("/api/prestasi-mahasiswa/{$prestasi->id}", [
            'id_mahasiswa' => $mahasiswa->id,
            'nama_aktivitas' => 'Juara Baru',
            'tahun' => 2024,
            'evidence' => [
                [
                    'id' => $existingEvidence1->id,
                    'url' => 'https://storage.polines.ac.id/updated-1.pdf',
                    'jenis_evidence' => 'Piagam',
                    'judul' => 'Updated 1',
                ],
                [
                    'url' => 'https://storage.polines.ac.id/new-3.pdf',
                    'jenis_evidence' => 'Publikasi',
                    'judul' => 'New 3',
                ],
            ],
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

    // Evidence 1 diupdate
    $this->assertDatabaseHas('evidence_prestasi_mahasiswa', [
        'id' => $existingEvidence1->id,
        'url' => 'https://storage.polines.ac.id/updated-1.pdf',
        'jenis_evidence' => 'Piagam',
    ]);

    // Evidence 2 di-remove karena tidak disertakan dalam array
    $this->assertDatabaseMissing('evidence_prestasi_mahasiswa', [
        'id' => $existingEvidence2->id,
    ]);

    // Evidence 3 baru ditambahkan
    $this->assertDatabaseHas('evidence_prestasi_mahasiswa', [
        'id_prestasi_mahasiswa' => $prestasi->id,
        'url' => 'https://storage.polines.ac.id/new-3.pdf',
        'jenis_evidence' => 'Publikasi',
    ]);
});

test('updating prestasi mahasiswa without evidence key leaves existing evidence intact', function () {
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

    $evidence = EvidencePrestasiMahasiswa::create([
        'id_prestasi_mahasiswa' => $prestasi->id,
        'url' => 'https://storage.polines.ac.id/keep-me.pdf',
        'jenis_evidence' => 'Sertifikat',
        'judul' => 'Tetap Ada',
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->putJson("/api/prestasi-mahasiswa/{$prestasi->id}", [
            'id_mahasiswa' => $mahasiswa->id,
            'nama_aktivitas' => 'Juara Terupdate',
            'tahun' => 2024,
        ]);

    $response->assertStatus(200);

    $this->assertDatabaseHas('evidence_prestasi_mahasiswa', [
        'id' => $evidence->id,
        'url' => 'https://storage.polines.ac.id/keep-me.pdf',
    ]);
});

test('authenticated user can delete prestasi mahasiswa and its child evidence', function () {
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

    $evidence = EvidencePrestasiMahasiswa::create([
        'id_prestasi_mahasiswa' => $prestasi->id,
        'url' => 'https://storage.polines.ac.id/delete-me.pdf',
        'jenis_evidence' => 'Sertifikat',
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

    $this->assertDatabaseMissing('evidence_prestasi_mahasiswa', [
        'id' => $evidence->id,
    ]);
});
