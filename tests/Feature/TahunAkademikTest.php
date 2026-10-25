<?php

use App\Models\Angkatan;
use App\Models\AngkatanKelas;
use App\Models\EnrollmentKelas;
use App\Models\Kelas;
use App\Models\Mahasiswa;
use App\Models\Prodi;
use App\Models\TahunAkademik;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated user can list and create tahun akademik', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    TahunAkademik::create([
        'tahun' => '2024/2025',
        'semester' => 'Ganjil',
        'is_active' => true,
    ]);

    // Test GET all tahun akademik
    $responseGet = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/tahun-akademik');

    $responseGet->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'code',
            'message',
            'data',
        ]);

    // Test POST create tahun akademik
    $payload = [
        'tahun' => '2026/2027',
        'semester' => 'Genap',
        'is_active' => false,
    ];

    $responsePost = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/tahun-akademik', $payload);

    $responsePost->assertStatus(201)
        ->assertJson([
            'success' => true,
            'message' => 'Tahun akademik berhasil ditambahkan',
        ]);

    $this->assertDatabaseHas('tahun_akademik', [
        'tahun' => '2026/2027',
    ]);
});

test('can create both Ganjil and Genap semesters for the same academic year', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $resGanjil = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/tahun-akademik', [
            'tahun' => '2025/2026',
            'semester' => 'Ganjil',
            'is_active' => true,
        ]);
    $resGanjil->assertStatus(201);

    $resGenap = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/tahun-akademik', [
            'tahun' => '2025/2026',
            'semester' => 'Genap',
            'is_active' => false,
        ]);
    $resGenap->assertStatus(201);

    $this->assertDatabaseHas('tahun_akademik', [
        'tahun' => '2025/2026',
        'semester' => 'Ganjil',
    ]);
    $this->assertDatabaseHas('tahun_akademik', [
        'tahun' => '2025/2026',
        'semester' => 'Genap',
    ]);
});

test('cannot create duplicate semester in the same academic year', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    TahunAkademik::create([
        'tahun' => '2025/2026',
        'semester' => 'Ganjil',
        'is_active' => true,
    ]);

    $res = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/tahun-akademik', [
            'tahun' => '2025/2026',
            'semester' => 'Ganjil',
            'is_active' => true,
        ]);

    $res->assertStatus(422)
        ->assertJsonValidationErrors(['tahun']);
});

test('can filter tahun akademik by semester and is_active', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    TahunAkademik::create(['tahun' => '2024/2025', 'semester' => 'Ganjil', 'is_active' => false]);
    TahunAkademik::create(['tahun' => '2024/2025', 'semester' => 'Genap', 'is_active' => false]);
    TahunAkademik::create(['tahun' => '2025/2026', 'semester' => 'Ganjil', 'is_active' => true]);

    $resActive = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/tahun-akademik?is_active=true');

    $resActive->assertStatus(200);
    $dataActive = $resActive->json('data');
    expect(count($dataActive))->toBe(1);
    expect($dataActive[0]['tahun'])->toBe('2025/2026');

    $resGenap = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/tahun-akademik?semester=Genap');

    $resGenap->assertStatus(200);
    $dataGenap = $resGenap->json('data');
    expect(count($dataGenap))->toBe(1);
    expect($dataGenap[0]['semester'])->toBe('Genap');
});

test('tahun akademik index and kelas endpoint return class count and per-year student counts', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $prodi = Prodi::create([
        'kode_prodi' => 'PR01',
        'nama_prodi' => 'Teknologi Rekayasa Komputer',
        'akreditasi' => 'Baik Sekali',
    ]);
    $ta2025 = TahunAkademik::create(['tahun' => '2025/2026', 'semester' => 'Ganjil', 'is_active' => false]);
    $ta2026 = TahunAkademik::create(['tahun' => '2026/2027', 'semester' => 'Ganjil', 'is_active' => true]);

    $kelas1A = Kelas::create(['kelas' => 'TI-1A']);
    $kelas2A = Kelas::create(['kelas' => 'TI-2A']);

    $ang2025 = Angkatan::create(['angkatan' => 2025]);
    $ang2026 = Angkatan::create(['angkatan' => 2026]);

    // Pada 2025/2026: TI-1A diisi Angkatan 2025
    $ak2025_1A = AngkatanKelas::create([
        'tahun_akademik_id' => $ta2025->id,
        'kelas_id' => $kelas1A->id,
        'angkatan_id' => $ang2025->id,
        'prodi_id' => $prodi->id,
    ]);

    // Pada 2026/2027: TI-1A diisi Angkatan 2026, TI-2A diisi Angkatan 2025
    $ak2026_1A = AngkatanKelas::create([
        'tahun_akademik_id' => $ta2026->id,
        'kelas_id' => $kelas1A->id,
        'angkatan_id' => $ang2026->id,
        'prodi_id' => $prodi->id,
    ]);
    $ak2026_2A = AngkatanKelas::create([
        'tahun_akademik_id' => $ta2026->id,
        'kelas_id' => $kelas2A->id,
        'angkatan_id' => $ang2025->id,
        'prodi_id' => $prodi->id,
    ]);

    $mhs25 = Mahasiswa::create([
        'nim' => 43325006,
        'nama_mahasiswa' => 'Dasi Hayu Permana',
        'tahun_masuk' => 2025,
        'jenis_pendaftaran' => 'SNBT',
        'masa_studi_bulan' => 48,
    ]);
    $mhs26 = Mahasiswa::create([
        'nim' => 43326001,
        'nama_mahasiswa' => 'Mahasiswa Baru 2026',
        'tahun_masuk' => 2026,
        'jenis_pendaftaran' => 'SNBP',
        'masa_studi_bulan' => 48,
    ]);

    EnrollmentKelas::create([
        'id_mahasiswa' => $mhs25->id,
        'angkatan_kelas_id' => $ak2025_1A->id,
        'prodi_id' => $prodi->id,
    ]);
    EnrollmentKelas::create([
        'id_mahasiswa' => $mhs25->id,
        'angkatan_kelas_id' => $ak2026_2A->id,
        'prodi_id' => $prodi->id,
    ]);
    EnrollmentKelas::create([
        'id_mahasiswa' => $mhs26->id,
        'angkatan_kelas_id' => $ak2026_1A->id,
        'prodi_id' => $prodi->id,
    ]);

    // 1. Cek jumlah_kelas pada index tahun akademik
    $resTa = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/tahun-akademik?is_active=true');
    $resTa->assertStatus(200)
        ->assertJsonPath('data.0.jumlah_kelas', 2)
        ->assertJsonPath('data.0.prodi.nama', 'Teknologi Rekayasa Komputer');

    // 2. Cek mahasiswa TI-1A pada 2025/2026 vs 2026/2027 berbeda sesuai angkatan
    $resMhs2025 = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson("/api/kelas/{$kelas1A->id}/mahasiswa?tahun_akademik_id={$ta2025->id}");
    $resMhs2025->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.nama_mahasiswa', 'Dasi Hayu Permana');

    $resMhs2026 = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson("/api/kelas/{$kelas1A->id}/mahasiswa?tahun_akademik_id={$ta2026->id}");
    $resMhs2026->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.nama_mahasiswa', 'Mahasiswa Baru 2026');
});
