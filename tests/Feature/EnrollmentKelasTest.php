<?php

use App\Models\Kelas;
use App\Models\Angkatan;
use App\Models\Prodi;
use App\Models\TahunAkademik;
use App\Models\AngkatanKelas;
use App\Models\Mahasiswa;
use App\Models\EnrollmentKelas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// Helper function untuk menyiapkan data parent yang rumit
function setupEnrollmentDependencies() {
    $prodi = Prodi::create(['kode_prodi' => 'PR-EN', 'nama_prodi' => 'Teknik Elektro', 'akreditasi' => 'Unggul']);
    $ta = TahunAkademik::create(['tahun' => '2026']);
    $kelas = Kelas::create(['kelas' => 'TE-1A']);
    $angkatan = Angkatan::create(['angkatan' => 2026]);
    
    $angkatanKelas = AngkatanKelas::create([
        'kelas_id'          => $kelas->id,
        'angkatan_id'       => $angkatan->id,
        'prodi_id'          => $prodi->id,
        'tahun_akademik_id' => $ta->id,
    ]);

    $mahasiswa = Mahasiswa::create([
        'nim'               => 12345678,
        'nama_mahasiswa'    => 'Mahasiswa Enrollment',
        'tahun_masuk'       => 2026,
        'jenis_pendaftaran' => 'Reguler',
        'masa_studi_bulan'  => 48,
    ]);

    return [$prodi, $angkatanKelas, $mahasiswa];
}

test('unauthenticated user cannot access enrollment endpoints', function () {
    $this->getJson('/api/enrollment-kelas')->assertStatus(401);
});

test('authenticated user can list enrollment', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    [$prodi, $angkatanKelas, $mahasiswa] = setupEnrollmentDependencies();
    
    EnrollmentKelas::create([
        'id_mahasiswa'      => $mahasiswa->id,
        'angkatan_kelas_id' => $angkatanKelas->id,
        'prodi_id'          => $prodi->id,
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/enrollment-kelas');

    $response->assertStatus(200)->assertJson(['success' => true]);
    expect($response->json('data'))->toHaveCount(1);
});

test('authenticated user can create enrollment', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    [$prodi, $angkatanKelas, $mahasiswa] = setupEnrollmentDependencies();

    $payload = [
        'id_mahasiswa'      => $mahasiswa->id,
        'angkatan_kelas_id' => $angkatanKelas->id,
        'prodi_id'          => $prodi->id,
    ];

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/enrollment-kelas', $payload);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'message' => 'Data Enrollment berhasil ditambahkan',
        ]);

    $this->assertDatabaseHas('enrollment_mahasiswa_angkatan_kelas', [
        'id_mahasiswa' => $mahasiswa->id,
        'angkatan_kelas_id' => $angkatanKelas->id,
    ]);
});

test('authenticated user can view detail, update, and delete enrollment', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    [$prodi, $angkatanKelas, $mahasiswa] = setupEnrollmentDependencies();

    // Buat mahasiswa kedua untuk tes update
    $mahasiswaDua = Mahasiswa::create([
        'nim'               => 87654321,
        'nama_mahasiswa'    => 'Mahasiswa Pengganti',
        'tahun_masuk'       => 2026,
        'jenis_pendaftaran' => 'Reguler',
        'masa_studi_bulan'  => 48,
    ]);

    $enrollment = EnrollmentKelas::create([
        'id_mahasiswa'      => $mahasiswa->id,
        'angkatan_kelas_id' => $angkatanKelas->id,
        'prodi_id'          => $prodi->id,
    ]);

    // View
    $this->withHeader('Authorization', "Bearer {$token}")
         ->getJson("/api/enrollment-kelas/{$enrollment->id}")
         ->assertStatus(200)
         ->assertJsonPath('data.id_mahasiswa', $mahasiswa->id);

    // Update (ganti mahasiswa_id ke mahasiswaDua)
    $this->withHeader('Authorization', "Bearer {$token}")
         ->putJson("/api/enrollment-kelas/{$enrollment->id}", [
            'id_mahasiswa'      => $mahasiswaDua->id,
            'angkatan_kelas_id' => $angkatanKelas->id,
            'prodi_id'          => $prodi->id,
         ])
         ->assertStatus(200);

    $this->assertDatabaseHas('enrollment_mahasiswa_angkatan_kelas', [
        'id'           => $enrollment->id,
        'id_mahasiswa' => $mahasiswaDua->id,
    ]);

    // Delete
    $this->withHeader('Authorization', "Bearer {$token}")
         ->deleteJson("/api/enrollment-kelas/{$enrollment->id}")
         ->assertStatus(200);

    $this->assertDatabaseMissing('enrollment_mahasiswa_angkatan_kelas', [
        'id' => $enrollment->id,
    ]);
});