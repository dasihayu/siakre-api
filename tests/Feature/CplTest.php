<?php

use App\Models\Cpl;
use App\Models\Kurikulum;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('unauthenticated user cannot access cpl endpoints', function () {
    $this->getJson('/api/cpl')->assertStatus(401);
});

test('authenticated user can list cpl', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $prodi = Prodi::create(['kode_prodi' => 'PR01', 'nama_prodi' => 'Informatika', 'akreditasi' => 'Unggul']);
    $kurikulum = Kurikulum::create(['id_prodi' => $prodi->id, 'nama_kurikulum' => 'K24', 'berlaku_sampai' => 2028, 'sk_kurikulum' => 'SK01']);

    Cpl::create([
        'id_kurikulum' => $kurikulum->id,
        'kode_cpl' => 'CPL-01',
        'deskripsi_cpl' => 'Deskripsi CPL Tes',
        'kategori' => 'Sikap',
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/cpl');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Berhasil mengambil data CPL',
        ]);

    expect($response->json('data'))->toHaveCount(1);
});

test('authenticated user can create cpl with valid kategori enum', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $prodi = Prodi::create(['kode_prodi' => 'PR01', 'nama_prodi' => 'Informatika', 'akreditasi' => 'Unggul']);
    $kurikulum = Kurikulum::create(['id_prodi' => $prodi->id, 'nama_kurikulum' => 'K24', 'berlaku_sampai' => 2028, 'sk_kurikulum' => 'SK01']);

    $payload = [
        'id_kurikulum' => $kurikulum->id,
        'kode_cpl' => 'CPL-02',
        'deskripsi_cpl' => 'Mampu menganalisis data',
        'kategori' => 'Keterampilan Khusus',
    ];

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/cpl', $payload);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'message' => 'Data CPL berhasil ditambahkan',
            'data' => [
                'kode_cpl' => 'CPL-02',
                'kategori' => 'Keterampilan Khusus',
            ],
        ]);

    $this->assertDatabaseHas('cpl', [
        'kode_cpl' => 'CPL-02',
        'kategori' => 'Keterampilan Khusus',
    ]);
});

test('system allows identical kode cpl for different kurikulum', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $prodi = Prodi::create(['kode_prodi' => 'PR01', 'nama_prodi' => 'Informatika', 'akreditasi' => 'Unggul']);
    $kurikulum1 = Kurikulum::create(['id_prodi' => $prodi->id, 'nama_kurikulum' => 'K20', 'berlaku_sampai' => 2024, 'sk_kurikulum' => 'SK20']);
    $kurikulum2 = Kurikulum::create(['id_prodi' => $prodi->id, 'nama_kurikulum' => 'K24', 'berlaku_sampai' => 2028, 'sk_kurikulum' => 'SK24']);

    // CPL di Kurikulum 1
    Cpl::create([
        'id_kurikulum' => $kurikulum1->id,
        'kode_cpl' => 'CPL-01',
        'deskripsi_cpl' => 'Deskripsi K20',
        'kategori' => 'Sikap',
    ]);

    // Request buat CPL dengan kode yang SAMA ('CPL-01') di Kurikulum 2
    $payload = [
        'id_kurikulum' => $kurikulum2->id,
        'kode_cpl' => 'CPL-01',
        'deskripsi_cpl' => 'Deskripsi K24',
        'kategori' => 'Pengetahuan',
    ];

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/cpl', $payload);

    // Harus berhasil karena unik per kurikulum
    $response->assertStatus(201);
    $this->assertDatabaseHas('cpl', [
        'id_kurikulum' => $kurikulum2->id,
        'kode_cpl' => 'CPL-01',
    ]);
});

test('system blocks creation of duplicate kode cpl within the same kurikulum', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $prodi = Prodi::create(['kode_prodi' => 'PR01', 'nama_prodi' => 'Informatika', 'akreditasi' => 'Unggul']);
    $kurikulum = Kurikulum::create(['id_prodi' => $prodi->id, 'nama_kurikulum' => 'K24', 'berlaku_sampai' => 2028, 'sk_kurikulum' => 'SK01']);

    Cpl::create([
        'id_kurikulum' => $kurikulum->id,
        'kode_cpl' => 'CPL-DUPLIKAT',
        'deskripsi_cpl' => 'Deskripsi Awal',
        'kategori' => 'Sikap',
    ]);

    $payload = [
        'id_kurikulum' => $kurikulum->id,
        'kode_cpl' => 'CPL-DUPLIKAT',
        'deskripsi_cpl' => 'Deskripsi Baru',
        'kategori' => 'Pengetahuan',
    ];

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/cpl', $payload);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['kode_cpl']);
});

test('system blocks cpl creation with invalid kategori enum', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $prodi = Prodi::create(['kode_prodi' => 'PR01', 'nama_prodi' => 'Informatika', 'akreditasi' => 'Unggul']);
    $kurikulum = Kurikulum::create(['id_prodi' => $prodi->id, 'nama_kurikulum' => 'K24', 'berlaku_sampai' => 2028, 'sk_kurikulum' => 'SK01']);

    $payload = [
        'id_kurikulum' => $kurikulum->id,
        'kode_cpl' => 'CPL-INV',
        'deskripsi_cpl' => 'Deskripsi',
        'kategori' => 'Kategori Sembarang', // tidak valid
    ];

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/cpl', $payload);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['kategori']);
});

test('authenticated user can view detail of cpl', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $prodi = Prodi::create(['kode_prodi' => 'PR01', 'nama_prodi' => 'Informatika', 'akreditasi' => 'Unggul']);
    $kurikulum = Kurikulum::create(['id_prodi' => $prodi->id, 'nama_kurikulum' => 'K24', 'berlaku_sampai' => 2028, 'sk_kurikulum' => 'SK01']);

    $cpl = Cpl::create([
        'id_kurikulum' => $kurikulum->id,
        'kode_cpl' => 'CPL-03',
        'deskripsi_cpl' => 'Detail CPL',
        'kategori' => 'Keterampilan Umum',
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson("/api/cpl/{$cpl->id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'data' => [
                'id' => $cpl->id,
                'kode_cpl' => 'CPL-03',
                'kategori' => 'Keterampilan Umum',
            ],
        ]);
});

test('authenticated user can update cpl keeping same kode cpl', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $prodi = Prodi::create(['kode_prodi' => 'PR01', 'nama_prodi' => 'Informatika', 'akreditasi' => 'Unggul']);
    $kurikulum = Kurikulum::create(['id_prodi' => $prodi->id, 'nama_kurikulum' => 'K24', 'berlaku_sampai' => 2028, 'sk_kurikulum' => 'SK01']);

    $cpl = Cpl::create([
        'id_kurikulum' => $kurikulum->id,
        'kode_cpl' => 'CPL-04',
        'deskripsi_cpl' => 'Lama',
        'kategori' => 'Sikap',
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->putJson("/api/cpl/{$cpl->id}", [
            'id_kurikulum' => $kurikulum->id,
            'kode_cpl' => 'CPL-04',
            'deskripsi_cpl' => 'Baru',
            'kategori' => 'Pengetahuan',
        ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Data CPL berhasil diupdate',
        ]);

    $this->assertDatabaseHas('cpl', [
        'id' => $cpl->id,
        'kode_cpl' => 'CPL-04',
        'deskripsi_cpl' => 'Baru',
        'kategori' => 'Pengetahuan',
    ]);
});

test('system blocks updating cpl to duplicate kode within same kurikulum', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $prodi = Prodi::create(['kode_prodi' => 'PR01', 'nama_prodi' => 'Informatika', 'akreditasi' => 'Unggul']);
    $kurikulum = Kurikulum::create(['id_prodi' => $prodi->id, 'nama_kurikulum' => 'K24', 'berlaku_sampai' => 2028, 'sk_kurikulum' => 'SK01']);

    Cpl::create([
        'id_kurikulum' => $kurikulum->id,
        'kode_cpl' => 'CPL-EXIST',
        'deskripsi_cpl' => 'Exist',
        'kategori' => 'Sikap',
    ]);

    $target = Cpl::create([
        'id_kurikulum' => $kurikulum->id,
        'kode_cpl' => 'CPL-TARGET',
        'deskripsi_cpl' => 'Target',
        'kategori' => 'Sikap',
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->putJson("/api/cpl/{$target->id}", [
            'id_kurikulum' => $kurikulum->id,
            'kode_cpl' => 'CPL-EXIST',
            'deskripsi_cpl' => 'Coba ganti ke kode kembar',
            'kategori' => 'Pengetahuan',
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['kode_cpl']);
});

test('authenticated user can delete cpl', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $prodi = Prodi::create(['kode_prodi' => 'PR01', 'nama_prodi' => 'Informatika', 'akreditasi' => 'Unggul']);
    $kurikulum = Kurikulum::create(['id_prodi' => $prodi->id, 'nama_kurikulum' => 'K24', 'berlaku_sampai' => 2028, 'sk_kurikulum' => 'SK01']);

    $cpl = Cpl::create([
        'id_kurikulum' => $kurikulum->id,
        'kode_cpl' => 'CPL-HAPUS',
        'deskripsi_cpl' => 'Akan Dihapus',
        'kategori' => 'Sikap',
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->deleteJson("/api/cpl/{$cpl->id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Data berhasil dihapus',
        ]);

    $this->assertDatabaseMissing('cpl', [
        'id' => $cpl->id,
    ]);
});
