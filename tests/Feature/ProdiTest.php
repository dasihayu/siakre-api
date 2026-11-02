<?php

use App\Models\Kurikulum;
use App\Models\PerguruanTinggi;
use App\Models\Prodi;
use App\Models\Upps;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('unauthenticated user cannot access prodi endpoints', function () {
    $this->getJson('/api/prodi')->assertStatus(401);
});

test('authenticated user can list prodi', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $pt = PerguruanTinggi::create(['kode_pt' => 'PT001', 'nama_pt' => 'Polines']);
    $prodi = Prodi::create([
        'kode_prodi' => 'PR01',
        'nama_prodi' => 'Teknik Informatika',
        'akreditasi' => 'Unggul',
        'jenjang' => 'D4',
        'sk_pendirian' => 'SK-01',
        'tanggal_sk_pendirian' => '2020-01-01',
        'status_akreditasi' => 'Terakreditasi Unggul',
        'masa_berlaku_akreditasi' => '2028-12-31',
    ]);

    $upps = Upps::create([
        'id_pt' => $pt->id,
        'kode_upps' => 'UPPS01',
        'nama_upps' => 'Jurusan Elektro',
        'prodi_id' => $prodi->id,
        'jenis' => 'Jurusan',
    ]);

    $prodi->update(['id_upps' => $upps->id]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/prodi');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Berhasil mengambil data Prodi',
        ]);

    expect($response->json('data'))->toHaveCount(1);
    expect($response->json('data.0.upps.id'))->toBe($upps->id);
    expect($response->json('data.0.jenjang'))->toBe('D4');
});

test('authenticated user can create prodi with new fields', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $pt = PerguruanTinggi::create(['kode_pt' => 'PT001', 'nama_pt' => 'Polines']);
    $initialProdi = Prodi::create([
        'kode_prodi' => 'PR-INIT',
        'nama_prodi' => 'Init Prodi',
        'akreditasi' => 'A',
    ]);
    $upps = Upps::create([
        'id_pt' => $pt->id,
        'kode_upps' => 'UPPS01',
        'nama_upps' => 'Jurusan Elektro',
        'prodi_id' => $initialProdi->id,
        'jenis' => 'Jurusan',
    ]);

    $payload = [
        'kode_prodi' => 'PR02',
        'nama_prodi' => 'Sistem Informasi',
        'akreditasi' => 'Baik Sekali',
        'jenjang' => 'D3',
        'sk_pendirian' => 'SK-SI-002',
        'tanggal_sk_pendirian' => '2021-05-10',
        'status_akreditasi' => 'Terakreditasi',
        'masa_berlaku_akreditasi' => '2026-05-10',
        'id_upps' => $upps->id,
    ];

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/prodi', $payload);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'message' => 'Data Prodi berhasil ditambahkan',
            'data' => [
                'kode_prodi' => 'PR02',
                'nama_prodi' => 'Sistem Informasi',
                'jenjang' => 'D3',
                'status_akreditasi' => 'Terakreditasi',
                'id_upps' => $upps->id,
            ],
        ]);

    $this->assertDatabaseHas('prodi', [
        'kode_prodi' => 'PR02',
        'jenjang' => 'D3',
        'sk_pendirian' => 'SK-SI-002',
        'status_akreditasi' => 'Terakreditasi',
        'id_upps' => $upps->id,
    ]);
});

test('system blocks creation of duplicate kode prodi', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    Prodi::create([
        'kode_prodi' => 'PR-DUP',
        'nama_prodi' => 'Prodi Pertama',
        'jenjang' => 'D4',
    ]);

    $payload = [
        'kode_prodi' => 'PR-DUP',
        'nama_prodi' => 'Prodi Kedua',
        'jenjang' => 'D4',
    ];

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/prodi', $payload);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['kode_prodi']);
});

test('system blocks creation with invalid jenjang or status akreditasi', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $payload = [
        'kode_prodi' => 'PR-INV',
        'nama_prodi' => 'Prodi Invalid',
        'jenjang' => 'SMA', // invalid
        'status_akreditasi' => 'Belum Terakreditasi', // invalid
    ];

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/prodi', $payload);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['jenjang', 'status_akreditasi']);
});

test('authenticated user can view detail of prodi', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $prodi = Prodi::create([
        'kode_prodi' => 'PR03',
        'nama_prodi' => 'Teknik Komputer',
        'akreditasi' => 'Baik',
        'jenjang' => 'D4',
        'status_akreditasi' => 'Terakreditasi Unggul',
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson("/api/prodi/{$prodi->id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'data' => [
                'id' => $prodi->id,
                'kode_prodi' => 'PR03',
                'jenjang' => 'D4',
                'status_akreditasi' => 'Terakreditasi Unggul',
            ],
        ]);
});

test('authenticated user can update prodi', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $pt = PerguruanTinggi::create(['kode_pt' => 'PT001', 'nama_pt' => 'Polines']);
    $prodi = Prodi::create([
        'kode_prodi' => 'PR04',
        'nama_prodi' => 'Nama Lama',
        'akreditasi' => 'Baik',
        'jenjang' => 'D3',
    ]);

    $upps = Upps::create([
        'id_pt' => $pt->id,
        'kode_upps' => 'UPPS02',
        'nama_upps' => 'Jurusan Baru',
        'prodi_id' => $prodi->id,
        'jenis' => 'Jurusan',
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->putJson("/api/prodi/{$prodi->id}", [
            'kode_prodi' => 'PR04',
            'nama_prodi' => 'Nama Baru',
            'akreditasi' => 'Unggul',
            'jenjang' => 'D4',
            'sk_pendirian' => 'SK-UPDATE-004',
            'tanggal_sk_pendirian' => '2022-02-02',
            'status_akreditasi' => 'Terakreditasi Unggul',
            'masa_berlaku_akreditasi' => '2027-02-02',
            'id_upps' => $upps->id,
        ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Data Prodi berhasil diupdate',
            'data' => [
                'id' => $prodi->id,
                'nama_prodi' => 'Nama Baru',
                'jenjang' => 'D4',
                'status_akreditasi' => 'Terakreditasi Unggul',
            ],
        ]);

    $this->assertDatabaseHas('prodi', [
        'id' => $prodi->id,
        'nama_prodi' => 'Nama Baru',
        'jenjang' => 'D4',
        'sk_pendirian' => 'SK-UPDATE-004',
        'id_upps' => $upps->id,
    ]);
});

test('system blocks update of prodi with duplicate kode of another prodi', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    Prodi::create([
        'kode_prodi' => 'PR-EXISTING',
        'nama_prodi' => 'Prodi Existing',
        'jenjang' => 'D3',
    ]);

    $target = Prodi::create([
        'kode_prodi' => 'PR-TARGET',
        'nama_prodi' => 'Prodi Target',
        'jenjang' => 'D3',
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->putJson("/api/prodi/{$target->id}", [
            'kode_prodi' => 'PR-EXISTING',
            'nama_prodi' => 'Prodi Target Edit',
            'jenjang' => 'D3',
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['kode_prodi']);
});

test('authenticated user can delete prodi when not linked', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $prodi = Prodi::create([
        'kode_prodi' => 'PR99',
        'nama_prodi' => 'Prodi Hapus',
        'akreditasi' => 'C',
        'jenjang' => 'D3',
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->deleteJson("/api/prodi/{$prodi->id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Data berhasil dihapus',
        ]);

    $this->assertDatabaseMissing('prodi', [
        'id' => $prodi->id,
    ]);
});

test('system blocks deletion of prodi when linked to kurikulum', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $prodi = Prodi::create([
        'kode_prodi' => 'PR-LINKED',
        'nama_prodi' => 'Prodi Terikat',
        'jenjang' => 'D4',
    ]);

    Kurikulum::create([
        'id_prodi' => $prodi->id,
        'nama_kurikulum' => 'Kurikulum TRK',
        'berlaku_sampai' => 2028,
        'sk_kurikulum' => 'SK-01',
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->deleteJson("/api/prodi/{$prodi->id}");

    $response->assertStatus(400)
        ->assertJson([
            'success' => false,
            'message' => 'Prodi Masih Digunakan',
        ]);

    $this->assertDatabaseHas('prodi', [
        'id' => $prodi->id,
    ]);
});
