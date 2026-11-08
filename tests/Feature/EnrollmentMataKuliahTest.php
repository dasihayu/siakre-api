<?php

use App\Models\EnrollmentMataKuliah;
use App\Models\Kurikulum;
use App\Models\MataKuliah;
use App\Models\Prodi;
use App\Models\TahunAkademik;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function setupEnrollmentMKDependencies()
{
    $prodi = Prodi::create(['kode_prodi' => 'PR-EMK', 'nama_prodi' => 'Teknik Elektro', 'akreditasi' => 'A']);
    $kurikulum = Kurikulum::create(['id_prodi' => $prodi->id, 'nama_kurikulum' => 'K24', 'berlaku_sampai' => 2028, 'sk_kurikulum' => 'SK01']);

    $mk = MataKuliah::create([
        'id_kurikulum' => $kurikulum->id,
        'kode_mata_kuliah' => 'MK-ENROLL',
        'nama_mata_kuliah' => 'Pemrograman Lanjut',
        'sks' => 3,
        // Kolom semester dihapus sesuai dengan skema mata_kuliah lu
    ]);

    // Diperbaiki: Menggunakan kolom 'tahun' sesuai skema tahun_akademik lu
    $ta = TahunAkademik::create(['tahun' => 2026]);

    return [$mk, $ta];
}

test('unauthenticated user cannot access enrollment mata kuliah endpoints', function () {
    $this->getJson('/api/enrollment-mata-kuliah')->assertStatus(401);
});

test('authenticated user can list enrollment mata kuliah', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    [$mk, $ta] = setupEnrollmentMKDependencies();

    EnrollmentMataKuliah::create([
        'tahun_akademik_id' => $ta->id,
        'mata_kuliah_id' => $mk->id,
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/enrollment-mata-kuliah');

    $response->assertStatus(200)->assertJson(['success' => true]);
    expect($response->json('data'))->toHaveCount(1);
});

test('authenticated user can create enrollment mata kuliah', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    [$mk, $ta] = setupEnrollmentMKDependencies();

    $payload = [
        'tahun_akademik_id' => $ta->id,
        'mata_kuliah_id' => $mk->id,
    ];

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/enrollment-mata-kuliah', $payload);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'message' => 'Berhasil menambahkan data',
        ]);

    $this->assertDatabaseHas('enrollment_mata_kuliah', [
        'tahun_akademik_id' => $ta->id,
        'mata_kuliah_id' => $mk->id,
    ]);
});

test('authenticated user can view detail, update, and delete enrollment mata kuliah', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    [$mk, $ta] = setupEnrollmentMKDependencies();

    $data = EnrollmentMataKuliah::create([
        'tahun_akademik_id' => $ta->id,
        'mata_kuliah_id' => $mk->id,
    ]);

    // View
    $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson("/api/enrollment-mata-kuliah/{$data->id}")
        ->assertStatus(200)
        ->assertJsonPath('data.mata_kuliah_id', $mk->id);

    // Update
    $this->withHeader('Authorization', "Bearer {$token}")
        ->putJson("/api/enrollment-mata-kuliah/{$data->id}", [
            'tahun_akademik_id' => $ta->id,
            'mata_kuliah_id' => $mk->id,
            'dosen_id' => 99,
        ])
        ->assertStatus(200);

    $this->assertDatabaseHas('enrollment_mata_kuliah', [
        'id' => $data->id,
        'dosen_id' => 99,
    ]);

    // Delete
    $this->withHeader('Authorization', "Bearer {$token}")
        ->deleteJson("/api/enrollment-mata-kuliah/{$data->id}")
        ->assertStatus(200);

    $this->assertDatabaseMissing('enrollment_mata_kuliah', [
        'id' => $data->id,
    ]);
});

test('authenticated user can create enrollment mata kuliah with structured learning evidence', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    [$mk, $ta] = setupEnrollmentMKDependencies();

    $payload = [
        'tahun_akademik_id' => $ta->id,
        'mata_kuliah_id' => $mk->id,
        'evidence' => [
            [
                'jenis_dokumen' => 'RPS',
                'url' => 'https://drive.google.com/file/d/rps-sample/view',
            ],
            [
                'jenis_dokumen' => 'Kontrak Kuliah',
                'url' => 'https://drive.google.com/file/d/kontrak-sample/view',
            ],
            [
                'jenis_dokumen' => 'Presensi',
                'url' => 'https://drive.google.com/file/d/presensi-sample/view',
            ],
            [
                'jenis_dokumen' => 'Soal Ujian',
                'url' => 'https://drive.google.com/file/d/soal-sample/view',
            ],
        ],
    ];

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/enrollment-mata-kuliah', $payload);

    $response->assertStatus(201)
        ->assertJsonPath('data.evidence.0.jenis_dokumen', 'RPS')
        ->assertJsonPath('data.evidence.0.url', 'https://drive.google.com/file/d/rps-sample/view')
        ->assertJsonPath('data.evidence.1.jenis_dokumen', 'Kontrak Kuliah')
        ->assertJsonPath('data.evidence.2.jenis_dokumen', 'Presensi')
        ->assertJsonPath('data.evidence.3.jenis_dokumen', 'Soal Ujian');

    $record = EnrollmentMataKuliah::find($response->json('data.id'));
    expect($record->evidence)->toBeArray()
        ->toHaveCount(4)
        ->and($record->evidence[0]['jenis_dokumen'])->toBe('RPS');
});

test('authenticated user can update enrollment mata kuliah with structured evidence', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    [$mk, $ta] = setupEnrollmentMKDependencies();

    $emk = EnrollmentMataKuliah::create([
        'tahun_akademik_id' => $ta->id,
        'mata_kuliah_id' => $mk->id,
        'evidence' => [
            [
                'jenis_dokumen' => 'RPS',
                'url' => 'https://example.com/rps-lama.pdf',
            ],
        ],
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->putJson("/api/enrollment-mata-kuliah/{$emk->id}", [
            'tahun_akademik_id' => $ta->id,
            'mata_kuliah_id' => $mk->id,
            'evidence' => [
                [
                    'jenis_dokumen' => 'RPS',
                    'url' => 'https://example.com/rps-baru.pdf',
                ],
                [
                    'jenis_dokumen' => 'Soal Ujian',
                    'url' => 'https://example.com/soal-uas.pdf',
                ],
            ],
        ]);

    $response->assertStatus(200)
        ->assertJsonPath('data.evidence.0.url', 'https://example.com/rps-baru.pdf')
        ->assertJsonPath('data.evidence.1.jenis_dokumen', 'Soal Ujian');
});

test('cannot create enrollment mata kuliah with invalid document type in evidence', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    [$mk, $ta] = setupEnrollmentMKDependencies();

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/enrollment-mata-kuliah', [
            'tahun_akademik_id' => $ta->id,
            'mata_kuliah_id' => $mk->id,
            'evidence' => [
                [
                    'jenis_dokumen' => 'Sertifikat Seminar', // Tidak valid
                    'url' => 'https://example.com/invalid.pdf',
                ],
            ],
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['evidence.0.jenis_dokumen']);
});

test('cannot create enrollment mata kuliah with integer evidence', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    [$mk, $ta] = setupEnrollmentMKDependencies();

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/enrollment-mata-kuliah', [
            'tahun_akademik_id' => $ta->id,
            'mata_kuliah_id' => $mk->id,
            'evidence' => 123, // Tipe integer lama
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['evidence']);
});

test('cannot create enrollment mata kuliah with invalid url in evidence', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    [$mk, $ta] = setupEnrollmentMKDependencies();

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/enrollment-mata-kuliah', [
            'tahun_akademik_id' => $ta->id,
            'mata_kuliah_id' => $mk->id,
            'evidence' => [
                [
                    'jenis_dokumen' => 'RPS',
                    'url' => 'bukan-sebuah-url-valid',
                ],
            ],
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['evidence.0.url']);
});

test('can normalize single object or associative evidence into structured array', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    [$mk, $ta] = setupEnrollmentMKDependencies();

    // Objek tunggal
    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/enrollment-mata-kuliah', [
            'tahun_akademik_id' => $ta->id,
            'mata_kuliah_id' => $mk->id,
            'evidence' => [
                'jenis_dokumen' => 'RPS',
                'url' => 'https://example.com/rps-single.pdf',
            ],
        ]);

    $response->assertStatus(201)
        ->assertJsonPath('data.evidence.0.jenis_dokumen', 'RPS')
        ->assertJsonPath('data.evidence.0.url', 'https://example.com/rps-single.pdf');
});
