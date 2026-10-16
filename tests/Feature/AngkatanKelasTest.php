<?php

use App\Models\Kelas;
use App\Models\Angkatan;
use App\Models\Prodi;
use App\Models\TahunAkademik;
use App\Models\AngkatanKelas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('unauthenticated user cannot access angkatan kelas endpoints', function () {
    $this->getJson('/api/angkatan-kelas')->assertStatus(401);
});

test('authenticated user can list angkatan kelas', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $prodi = Prodi::create(['kode_prodi' => 'PR01', 'nama_prodi' => 'Informatika', 'akreditasi' => 'Unggul']);
    $ta = TahunAkademik::create(['tahun' => '2026']);
    $kelas = Kelas::create(['kelas' => 'TI-2A']); // Sesuaikan kolom tabel kelas
    $angkatan = Angkatan::create(['angkatan' => 2024]);
    
    AngkatanKelas::create([
        'kelas_id'          => $kelas->id,
        'angkatan_id'       => $angkatan->id,
        'prodi_id'          => $prodi->id,
        'tahun_akademik_id' => $ta->id,
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/angkatan-kelas');

    $response->assertStatus(200)->assertJson(['success' => true]);
    expect($response->json('data'))->toHaveCount(1);
});

test('authenticated user can create angkatan kelas', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $prodi = Prodi::create(['kode_prodi' => 'PR02', 'nama_prodi' => 'Sistem Informasi', 'akreditasi' => 'Baik']);
    $ta = TahunAkademik::create(['tahun' => '2026']);
    $kelas = Kelas::create(['kelas' => 'SI-1B']);
    $angkatan = Angkatan::create(['angkatan' => 2026]);

    $payload = [
        'kelas_id'          => $kelas->id,
        'angkatan_id'       => $angkatan->id,
        'prodi_id'          => $prodi->id,
        'tahun_akademik_id' => $ta->id,
    ];

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/angkatan-kelas', $payload);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'message' => 'Data Angkatan Kelas berhasil ditambahkan',
        ]);

    $this->assertDatabaseHas('angkatan_kelas', [
        'kelas_id' => $kelas->id,
        'angkatan_id' => $angkatan->id,
    ]);
});

test('authenticated user can view detail, update, and delete angkatan kelas', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $prodi = Prodi::create(['kode_prodi' => 'PR03', 'nama_prodi' => 'Teknik Komputer', 'akreditasi' => 'Unggul']);
    $ta = TahunAkademik::create(['tahun' => '2027']);
    $kelas = Kelas::create(['kelas' => 'TK-3C']);
    $kelasBaru = Kelas::create(['kelas' => 'TK-3D']); // Untuk tes update
    $angkatan = Angkatan::create(['angkatan' => 2025]);

    $angkatanKelas = AngkatanKelas::create([
        'kelas_id'          => $kelas->id,
        'angkatan_id'       => $angkatan->id,
        'prodi_id'          => $prodi->id,
        'tahun_akademik_id' => $ta->id,
    ]);

    // View
    $this->withHeader('Authorization', "Bearer {$token}")
         ->getJson("/api/angkatan-kelas/{$angkatanKelas->id}")
         ->assertStatus(200)
         ->assertJsonPath('data.kelas_id', $kelas->id);

    // Update
    $this->withHeader('Authorization', "Bearer {$token}")
         ->putJson("/api/angkatan-kelas/{$angkatanKelas->id}", [
            'kelas_id'          => $kelasBaru->id,
            'angkatan_id'       => $angkatan->id,
            'prodi_id'          => $prodi->id,
            'tahun_akademik_id' => $ta->id,
         ])
         ->assertStatus(200);

    $this->assertDatabaseHas('angkatan_kelas', [
        'id'       => $angkatanKelas->id,
        'kelas_id' => $kelasBaru->id,
    ]);

    // Delete
    $this->withHeader('Authorization', "Bearer {$token}")
         ->deleteJson("/api/angkatan-kelas/{$angkatanKelas->id}")
         ->assertStatus(200);

    $this->assertDatabaseMissing('angkatan_kelas', [
        'id' => $angkatanKelas->id,
    ]);
});