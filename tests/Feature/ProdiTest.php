<?php

use App\Models\PerguruanTinggi;
use App\Models\Prodi;
use App\Models\Upps;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function prodiTestUpps(): Upps
{
    $pt = PerguruanTinggi::create(['kode_pt' => 'PT-PRODI', 'nama_pt' => 'PT Prodi']);

    return Upps::create([
        'id_pt' => $pt->id,
        'kode_upps' => 'UPPS-PRODI',
        'nama_upps' => 'UPPS Prodi',
        'jenis' => 'Fakultas',
    ]);
}

test('unauthenticated user cannot access prodi endpoints', function () {
    $this->getJson('/api/prodi')->assertStatus(401);
});

test('authenticated user can list prodi', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    Prodi::create([
        'id_upps' => prodiTestUpps()->id,
        'kode_prodi' => 'PR01',
        'nama_prodi' => 'Teknik Informatika',
        'akreditasi' => 'Unggul',
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/prodi');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Berhasil mengambil data Prodi',
        ]);

    expect($response->json('data'))->toHaveCount(1);
});

test('authenticated user can create prodi', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);
    $upps = prodiTestUpps();

    $payload = [
        'id_upps' => $upps->id,
        'kode_prodi' => 'PR02',
        'nama_prodi' => 'Sistem Informasi',
        'akreditasi' => 'Baik Sekali',
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
            ],
        ]);

    $this->assertDatabaseHas('prodi', [
        'kode_prodi' => 'PR02',
    ]);

    $this->withHeader('Authorization', '******')
        ->postJson('/api/prodi', [
            'id_upps' => $upps->id,
            'kode_prodi' => 'PR02B',
            'nama_prodi' => 'Teknik Komputer',
            'akreditasi' => 'Baik',
        ])
        ->assertCreated();

    $this->withHeader('Authorization', '******')
        ->getJson("/api/upps/{$upps->id}")
        ->assertJsonCount(2, 'data.prodis');
});

test('authenticated user can view detail of prodi', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $prodi = Prodi::create([
        'id_upps' => prodiTestUpps()->id,
        'kode_prodi' => 'PR03',
        'nama_prodi' => 'Teknik Komputer',
        'akreditasi' => 'Baik',
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson("/api/prodi/{$prodi->id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'data' => [
                'id' => $prodi->id,
                'kode_prodi' => 'PR03',
            ],
        ]);
});

test('authenticated user can update prodi', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $upps = prodiTestUpps();
    $prodi = Prodi::create([
        'id_upps' => $upps->id,
        'kode_prodi' => 'PR04',
        'nama_prodi' => 'Nama Lama',
        'akreditasi' => 'Baik',
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->putJson("/api/prodi/{$prodi->id}", [
            'id_upps' => $upps->id,
            'kode_prodi' => 'PR04',
            'nama_prodi' => 'Nama Baru',
            'akreditasi' => 'Unggul',
        ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Data Prodi berhasil diupdate',
        ]);

    $this->assertDatabaseHas('prodi', [
        'id' => $prodi->id,
        'nama_prodi' => 'Nama Baru',
    ]);
});

test('authenticated user can delete prodi when not linked', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $prodi = Prodi::create([
        'id_upps' => prodiTestUpps()->id,
        'kode_prodi' => 'PR99',
        'nama_prodi' => 'Prodi Hapus',
        'akreditasi' => 'C',
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
