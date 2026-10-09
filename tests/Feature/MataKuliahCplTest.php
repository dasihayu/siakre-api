<?php

use App\Models\Prodi;
use App\Models\Kurikulum;
use App\Models\Cpl;
use App\Models\MataKuliah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated user can view cpl mapping of a mata kuliah', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $prodi = Prodi::create(['kode_prodi' => 'PR01', 'nama_prodi' => 'Informatika', 'akreditasi' => 'Unggul']);
    $kurikulum = Kurikulum::create(['id_prodi' => $prodi->id, 'nama_kurikulum' => 'K24', 'berlaku_sampai' => 2028, 'sk_kurikulum' => 'SK01']);
    
    $matkul = MataKuliah::create([
        'id_kurikulum'     => $kurikulum->id,
        'kode_mata_kuliah' => 'IF101',
        'nama_mata_kuliah' => 'Pemrograman Web',
        'sks'              => 3,
    ]);

    $cpl = Cpl::create([
        'id_kurikulum'  => $kurikulum->id,
        'kode_cpl'      => 'CPL-01',
        'deskripsi_cpl' => 'Mampu ngoding',
        'kategori'      => 'Umum'
    ]);

    // Lampirkan relasi secara manual untuk tes awal
    $matkul->cpl()->attach($cpl->id);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson("/api/mata-kuliah/{$matkul->id}/cpl");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
        ]);

    expect($response->json('data'))->toHaveCount(1);
});

test('authenticated user can sync cpl mapping to a mata kuliah', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $prodi = Prodi::create(['kode_prodi' => 'PR01', 'nama_prodi' => 'Informatika', 'akreditasi' => 'Unggul']);
    $kurikulum = Kurikulum::create(['id_prodi' => $prodi->id, 'nama_kurikulum' => 'K24', 'berlaku_sampai' => 2028, 'sk_kurikulum' => 'SK01']);
    
    $matkul = MataKuliah::create([
        'id_kurikulum'     => $kurikulum->id,
        'kode_mata_kuliah' => 'IF101',
        'nama_mata_kuliah' => 'Pemrograman Web',
        'sks'              => 3,
    ]);

    $cpl1 = Cpl::create(['id_kurikulum' => $kurikulum->id, 'kode_cpl' => 'CPL-01', 'deskripsi_cpl' => 'Test 1', 'kategori' => 'Umum']);
    $cpl2 = Cpl::create(['id_kurikulum' => $kurikulum->id, 'kode_cpl' => 'CPL-02', 'deskripsi_cpl' => 'Test 2', 'kategori' => 'Umum']);

    $payload = [
        'cpl_ids' => [$cpl1->id, $cpl2->id]
    ];

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson("/api/mata-kuliah/{$matkul->id}/cpl/sync", $payload);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Mapping CPL Mata Kuliah berhasil disimpan',
        ]);

    $this->assertDatabaseHas('mata_kuliah_cpl', [
        'id_mata_kuliah' => $matkul->id,
        'id_cpl' => $cpl1->id,
    ]);
    
    $this->assertDatabaseHas('mata_kuliah_cpl', [
        'id_mata_kuliah' => $matkul->id,
        'id_cpl' => $cpl2->id,
    ]);
});