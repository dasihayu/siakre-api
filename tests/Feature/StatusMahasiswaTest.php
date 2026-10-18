<?php

use App\Models\Angkatan;
use App\Models\AngkatanKelas;
use App\Models\EnrollmentKelas;
use App\Models\Kelas;
use App\Models\Mahasiswa;
use App\Models\Prodi;
use App\Models\StatusMahasiswa;
use App\Models\TahunAkademik;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function setupStatusDependencies()
{
    $prodi = Prodi::create(['kode_prodi' => 'PR-ST', 'nama_prodi' => 'Sastra', 'akreditasi' => 'A']);
    $ta = TahunAkademik::create(['tahun' => '2026']);
    $kelas = Kelas::create(['kelas' => 'SA-1A']);
    $angkatan = Angkatan::create(['angkatan' => '2026']);

    $angkatanKelas = AngkatanKelas::create([
        'kelas_id' => $kelas->id,
        'angkatan_id' => $angkatan->id,
        'prodi_id' => $prodi->id,
        'tahun_akademik_id' => $ta->id,
    ]);

    $mahasiswa = Mahasiswa::create([
        'nim' => 11223344,
        'nama_mahasiswa' => 'Mahasiswa Status',
        'tahun_masuk' => 2026,
        'jenis_pendaftaran' => 'Reguler',
        'masa_studi_bulan' => 48,
    ]);

    $enrollment = EnrollmentKelas::create([
        'id_mahasiswa' => $mahasiswa->id,
        'angkatan_kelas_id' => $angkatanKelas->id,
        'prodi_id' => $prodi->id,
    ]);

    return $enrollment;
}

test('unauthenticated user cannot access status mahasiswa endpoints', function () {
    $this->getJson('/api/status-mahasiswa')->assertStatus(401);
});

test('authenticated user can list status mahasiswa', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $enrollment = setupStatusDependencies();

    StatusMahasiswa::create([
        'id_enrollment_mahasiswa_angkatan_kelas' => $enrollment->id,
        'status' => 'Cuti',
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/status-mahasiswa');

    $response->assertStatus(200)->assertJson(['success' => true]);
    expect($response->json('data'))->toHaveCount(1);
});

test('authenticated user can create status mahasiswa', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $enrollment = setupStatusDependencies();

    $payload = [
        'id_enrollment_mahasiswa_angkatan_kelas' => $enrollment->id,
        'status' => 'Aktif',
    ];

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/status-mahasiswa', $payload);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'message' => 'Data Status Mahasiswa berhasil ditambahkan',
        ]);

    $this->assertDatabaseHas('status_mahasiswa', [
        'id_enrollment_mahasiswa_angkatan_kelas' => $enrollment->id,
        'status' => 'Aktif',
    ]);
});

test('authenticated user can view detail, update, and delete status mahasiswa', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $enrollment = setupStatusDependencies();

    $status = StatusMahasiswa::create([
        'id_enrollment_mahasiswa_angkatan_kelas' => $enrollment->id,
        'status' => 'Aktif',
    ]);

    // View
    $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson("/api/status-mahasiswa/{$status->id}")
        ->assertStatus(200)
        ->assertJsonPath('data.status', 'Aktif');

    // Update
    $this->withHeader('Authorization', "Bearer {$token}")
        ->putJson("/api/status-mahasiswa/{$status->id}", [
            'id_enrollment_mahasiswa_angkatan_kelas' => $enrollment->id,
            'status' => 'Lulus',
        ])
        ->assertStatus(200);

    $this->assertDatabaseHas('status_mahasiswa', [
        'id' => $status->id,
        'status' => 'Lulus',
    ]);

    // Delete
    $this->withHeader('Authorization', "Bearer {$token}")
        ->deleteJson("/api/status-mahasiswa/{$status->id}")
        ->assertStatus(200);

    $this->assertDatabaseMissing('status_mahasiswa', [
        'id' => $status->id,
    ]);
});
