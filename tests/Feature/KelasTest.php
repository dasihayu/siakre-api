<?php

use App\Models\Angkatan;
use App\Models\AngkatanKelas;
use App\Models\EnrollmentKelas;
use App\Models\EnrollmentMataKuliah;
use App\Models\Kelas;
use App\Models\Kurikulum;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\MataKuliahAngkatanKelas;
use App\Models\Prodi;
use App\Models\TahunAkademik;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated user can list and create kelas', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    Kelas::create(['kelas' => 'TI-1A']);

    // Test GET all kelas
    $responseGet = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/kelas');

    $responseGet->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'code',
            'message',
            'data',
        ]);

    // Test POST create kelas
    $payload = ['kelas' => 'TI-2A'];

    $responsePost = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/kelas', $payload);

    $responsePost->assertStatus(201)
        ->assertJson([
            'success' => true,
            'message' => 'Kelas berhasil ditambahkan',
        ]);

    // Assert database has the newly created kelas after the POST request
    $this->assertDatabaseHas('kelas', [
        'kelas' => 'TI-2A',
    ]);
});
test('authenticated user can get mahasiswa per kelas', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $prodi = Prodi::create(['kode_prodi' => 'PR-KMHS', 'nama_prodi' => 'Teknik Informatika', 'akreditasi' => 'Unggul']);
    $ta = TahunAkademik::create(['tahun' => 2026]);
    $kelas = Kelas::create(['kelas' => 'TI-3A']);
    $angkatan = Angkatan::create(['angkatan' => 2024]);

    $ak = AngkatanKelas::create([
        'kelas_id' => $kelas->id,
        'angkatan_id' => $angkatan->id,
        'prodi_id' => $prodi->id,
        'tahun_akademik_id' => $ta->id,
    ]);

    $mhs = Mahasiswa::create([
        'nim' => 99998888,
        'nama_mahasiswa' => 'Mahasiswa Kelas Test',
        'tahun_masuk' => 2024,
        'jenis_pendaftaran' => 'Reguler',
        'masa_studi_bulan' => 48,
    ]);

    EnrollmentKelas::create([
        'id_mahasiswa' => $mhs->id,
        'angkatan_kelas_id' => $ak->id,
        'prodi_id' => $prodi->id,
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson("/api/kelas/{$kelas->id}/mahasiswa");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Berhasil mendapatkan data mahasiswa per kelas',
        ]);

    expect($response->json('data'))->toHaveCount(1);
    expect($response->json('data.0.nama_mahasiswa'))->toBe('Mahasiswa Kelas Test');

    // 404 test
    $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/kelas/99999/mahasiswa')
        ->assertStatus(404);
});

test('authenticated user can get mata kuliah per kelas', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $prodi = Prodi::create(['kode_prodi' => 'PR-KMK', 'nama_prodi' => 'Teknik Informatika', 'akreditasi' => 'Unggul']);
    $kurikulum = Kurikulum::create([
        'id_prodi' => $prodi->id,
        'nama_kurikulum' => 'Kurikulum TI',
        'berlaku_sampai' => 2028,
        'sk_kurikulum' => 'SK-TI',
    ]);
    $mk = MataKuliah::create([
        'id_kurikulum' => $kurikulum->id,
        'kode_mata_kuliah' => 'MK-KMK1',
        'nama_mata_kuliah' => 'Kecerdasan Buatan',
        'sks' => 3,
    ]);
    $ta = TahunAkademik::create(['tahun' => 2026]);
    $kelas = Kelas::create(['kelas' => 'TI-4A']);
    $angkatan = Angkatan::create(['angkatan' => 2023]);

    $ak = AngkatanKelas::create([
        'kelas_id' => $kelas->id,
        'angkatan_id' => $angkatan->id,
        'prodi_id' => $prodi->id,
        'tahun_akademik_id' => $ta->id,
    ]);

    $emk = EnrollmentMataKuliah::create([
        'tahun_akademik_id' => $ta->id,
        'mata_kuliah_id' => $mk->id,
    ]);

    MataKuliahAngkatanKelas::create([
        'enrollment_mata_kuliah_id' => $emk->id,
        'angkatan_kelas_id' => $ak->id,
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson("/api/kelas/{$kelas->id}/mata-kuliah");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Berhasil mendapatkan data mata kuliah per kelas',
        ]);

    expect($response->json('data'))->toHaveCount(1);
    expect($response->json('data.0.nama_mata_kuliah'))->toBe('Kecerdasan Buatan');

    // 404 test
    $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/kelas/99999/mata-kuliah')
        ->assertStatus(404);
});
