<?php

use App\Models\Prodi;
use App\Models\Kurikulum;
use App\Models\MataKuliah;
use App\Models\TahunAkademik;
use App\Models\EnrollmentMataKuliah;
use App\Models\Kelas;
use App\Models\Angkatan;
use App\Models\AngkatanKelas;
use App\Models\MataKuliahAngkatanKelas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function setupMKAngkatanKelasDependencies() {
    $prodi = Prodi::create(['kode_prodi' => 'PR-MKAK', 'nama_prodi' => 'Teknik Elektro', 'akreditasi' => 'A']);
    $kurikulum = Kurikulum::create(['id_prodi' => $prodi->id, 'nama_kurikulum' => 'K24', 'berlaku_sampai' => 2028, 'sk_kurikulum' => 'SK01']);
    
    $mk = MataKuliah::create([
        'id_kurikulum'     => $kurikulum->id,
        'kode_mata_kuliah' => 'MK-PIVOT',
        'nama_mata_kuliah' => 'Sistem Digital',
        'sks'              => 2,
    ]);

    $ta = TahunAkademik::create(['tahun' => 2026]);
    
    // PERBAIKAN: 'nama_kelas' diubah menjadi 'kelas' sesuai skema asli tabel kelas lu
    $kelas = Kelas::create(['kelas' => 'TE-1B']);
    $angkatan = Angkatan::create(['angkatan' => 2026]);

    $enrollmentMK = EnrollmentMataKuliah::create([
        'tahun_akademik_id' => $ta->id,
        'mata_kuliah_id'    => $mk->id,
    ]);

    $angkatanKelas = AngkatanKelas::create([
        'kelas_id'          => $kelas->id,
        'angkatan_id'       => $angkatan->id,
        'prodi_id'          => $prodi->id,
        'tahun_akademik_id' => $ta->id,
    ]);

    return [$enrollmentMK, $angkatanKelas];
}

test('unauthenticated user cannot access mata kuliah angkatan kelas endpoints', function () {
    $this->getJson('/api/mata-kuliah-angkatan-kelas')->assertStatus(401);
});

test('authenticated user can list mata kuliah angkatan kelas', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    [$emk, $ak] = setupMKAngkatanKelasDependencies();
    
    MataKuliahAngkatanKelas::create([
        'enrollment_mata_kuliah_id' => $emk->id,
        'angkatan_kelas_id'         => $ak->id,
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/mata-kuliah-angkatan-kelas');

    $response->assertStatus(200)->assertJson(['success' => true]);
    expect($response->json('data'))->toHaveCount(1);
});

test('authenticated user can create mata kuliah angkatan kelas', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    [$emk, $ak] = setupMKAngkatanKelasDependencies();

    $payload = [
        'enrollment_mata_kuliah_id' => $emk->id,
        'angkatan_kelas_id'         => $ak->id,
    ];

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/mata-kuliah-angkatan-kelas', $payload);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'message' => 'Berhasil menambahkan data',
        ]);

    $this->assertDatabaseHas('mata_kuliah_angkatan_kelas', [
        'enrollment_mata_kuliah_id' => $emk->id,
        'angkatan_kelas_id'         => $ak->id,
    ]);
});

test('authenticated user can view detail, update, and delete mata kuliah angkatan kelas', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    [$emk, $ak] = setupMKAngkatanKelasDependencies();

    $data = MataKuliahAngkatanKelas::create([
        'enrollment_mata_kuliah_id' => $emk->id,
        'angkatan_kelas_id'         => $ak->id,
    ]);

    // View
    $this->withHeader('Authorization', "Bearer {$token}")
         ->getJson("/api/mata-kuliah-angkatan-kelas/{$data->id}")
         ->assertStatus(200)
         ->assertJsonPath('data.enrollment_mata_kuliah_id', $emk->id);

    // Update
    $this->withHeader('Authorization', "Bearer {$token}")
         ->putJson("/api/mata-kuliah-angkatan-kelas/{$data->id}", [
            'enrollment_mata_kuliah_id' => $emk->id,
            'angkatan_kelas_id'         => $ak->id,
         ])
         ->assertStatus(200);

    // Delete
    $this->withHeader('Authorization', "Bearer {$token}")
         ->deleteJson("/api/mata-kuliah-angkatan-kelas/{$data->id}")
         ->assertStatus(200);

    $this->assertDatabaseMissing('mata_kuliah_angkatan_kelas', [
        'id' => $data->id,
    ]);
});