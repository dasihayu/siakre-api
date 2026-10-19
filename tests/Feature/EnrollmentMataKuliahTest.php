<?php

use App\Models\Prodi;
use App\Models\Kurikulum;
use App\Models\MataKuliah;
use App\Models\TahunAkademik;
use App\Models\EnrollmentMataKuliah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function setupEnrollmentMKDependencies() {
    $prodi = Prodi::create(['kode_prodi' => 'PR-EMK', 'nama_prodi' => 'Teknik Elektro', 'akreditasi' => 'A']);
    $kurikulum = Kurikulum::create(['id_prodi' => $prodi->id, 'nama_kurikulum' => 'K24', 'berlaku_sampai' => 2028, 'sk_kurikulum' => 'SK01']);
    
    $mk = MataKuliah::create([
        'id_kurikulum'     => $kurikulum->id,
        'kode_mata_kuliah' => 'MK-ENROLL',
        'nama_mata_kuliah' => 'Pemrograman Lanjut',
        'sks'              => 3,
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
        'mata_kuliah_id'    => $mk->id,
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
        'mata_kuliah_id'    => $mk->id,
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
        'mata_kuliah_id'    => $mk->id,
    ]);
});

test('authenticated user can view detail, update, and delete enrollment mata kuliah', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    [$mk, $ta] = setupEnrollmentMKDependencies();

    $data = EnrollmentMataKuliah::create([
        'tahun_akademik_id' => $ta->id,
        'mata_kuliah_id'    => $mk->id,
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
            'mata_kuliah_id'    => $mk->id,
            'dosen_id'          => 99,
         ])
         ->assertStatus(200);

    $this->assertDatabaseHas('enrollment_mata_kuliah', [
        'id'       => $data->id,
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