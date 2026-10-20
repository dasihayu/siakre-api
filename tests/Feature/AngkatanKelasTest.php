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
        'kelas_id' => $kelas->id,
        'angkatan_id' => $angkatan->id,
        'prodi_id' => $prodi->id,
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
        'kelas_id' => $kelas->id,
        'angkatan_id' => $angkatan->id,
        'prodi_id' => $prodi->id,
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
        'kelas_id' => $kelas->id,
        'angkatan_id' => $angkatan->id,
        'prodi_id' => $prodi->id,
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
            'kelas_id' => $kelasBaru->id,
            'angkatan_id' => $angkatan->id,
            'prodi_id' => $prodi->id,
            'tahun_akademik_id' => $ta->id,
        ])
        ->assertStatus(200);

    $this->assertDatabaseHas('angkatan_kelas', [
        'id' => $angkatanKelas->id,
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

test('authenticated user can get mahasiswa per angkatan kelas', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $prodi = Prodi::create(['kode_prodi' => 'PR-AKMHS', 'nama_prodi' => 'Teknik Elektro', 'akreditasi' => 'A']);
    $ta = TahunAkademik::create(['tahun' => 2026]);
    $kelas = Kelas::create(['kelas' => 'TE-1A']);
    $angkatan = Angkatan::create(['angkatan' => 2026]);

    $ak1 = AngkatanKelas::create([
        'kelas_id' => $kelas->id,
        'angkatan_id' => $angkatan->id,
        'prodi_id' => $prodi->id,
        'tahun_akademik_id' => $ta->id,
    ]);

    $ak2 = AngkatanKelas::create([
        'kelas_id' => $kelas->id,
        'angkatan_id' => $angkatan->id,
        'prodi_id' => $prodi->id,
        'tahun_akademik_id' => $ta->id,
    ]);

    $mhs1 = Mahasiswa::create([
        'nim' => 11112222,
        'nama_mahasiswa' => 'Mahasiswa Satu',
        'tahun_masuk' => 2026,
        'jenis_pendaftaran' => 'Reguler',
        'masa_studi_bulan' => 48,
    ]);

    $mhs2 = Mahasiswa::create([
        'nim' => 33334444,
        'nama_mahasiswa' => 'Mahasiswa Dua',
        'tahun_masuk' => 2026,
        'jenis_pendaftaran' => 'Reguler',
        'masa_studi_bulan' => 48,
    ]);

    EnrollmentKelas::create([
        'id_mahasiswa' => $mhs1->id,
        'angkatan_kelas_id' => $ak1->id,
        'prodi_id' => $prodi->id,
    ]);

    EnrollmentKelas::create([
        'id_mahasiswa' => $mhs2->id,
        'angkatan_kelas_id' => $ak2->id,
        'prodi_id' => $prodi->id,
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson("/api/angkatan-kelas/{$ak1->id}/mahasiswa");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Berhasil mengambil data Mahasiswa per Angkatan Kelas',
        ]);

    expect($response->json('data'))->toHaveCount(1);
    expect($response->json('data.0.nama_mahasiswa'))->toBe('Mahasiswa Satu');
    expect($response->json('data.0.enrollment_id'))->not->toBeNull();

    // 404 test
    $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/angkatan-kelas/99999/mahasiswa')
        ->assertStatus(404);
});

test('authenticated user can get mata kuliah per angkatan kelas', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $prodi = Prodi::create(['kode_prodi' => 'PR-AKMK', 'nama_prodi' => 'Teknik Elektro', 'akreditasi' => 'A']);
    $kurikulum = Kurikulum::create([
        'id_prodi' => $prodi->id,
        'nama_kurikulum' => 'K24',
        'berlaku_sampai' => 2028,
        'sk_kurikulum' => 'SK01',
    ]);
    $mk = MataKuliah::create([
        'id_kurikulum' => $kurikulum->id,
        'kode_mata_kuliah' => 'MK-AK1',
        'nama_mata_kuliah' => 'Elektronika Analog',
        'sks' => 3,
    ]);
    $ta = TahunAkademik::create(['tahun' => 2026]);
    $kelas = Kelas::create(['kelas' => 'TE-2A']);
    $angkatan = Angkatan::create(['angkatan' => 2026]);

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
        ->getJson("/api/angkatan-kelas/{$ak->id}/mata-kuliah");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Berhasil mengambil data Mata Kuliah per Angkatan Kelas',
        ]);

    expect($response->json('data'))->toHaveCount(1);
    expect($response->json('data.0.nama_mata_kuliah'))->toBe('Elektronika Analog');
    expect($response->json('data.0.mata_kuliah_angkatan_kelas_id'))->not->toBeNull();

    // 404 test
    $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/angkatan-kelas/99999/mata-kuliah')
        ->assertStatus(404);
});
