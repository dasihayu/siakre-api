<?php

use App\Models\Angkatan;
use App\Models\AngkatanKelas;
use App\Models\EnrollmentMataKuliah;
use App\Models\Kelas;
use App\Models\Kurikulum;
use App\Models\MataKuliah;
use App\Models\MataKuliahAngkatanKelas;
use App\Models\NilaiMahasiswa;
use App\Models\Prodi;
use App\Models\TahunAkademik;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function getAuthToken(): string
{
    $user = User::factory()->create();

    return auth('api')->login($user);
}

function createMataKuliahAngkatanKelas(): MataKuliahAngkatanKelas
{
    $prodi = Prodi::create([
        'kode_prodi' => 'PR-NILAI',
        'nama_prodi' => 'Teknologi Rekayasa Komputer',
        'akreditasi' => 'Unggul',
    ]);

    $kurikulum = Kurikulum::create([
        'id_prodi' => $prodi->id,
        'nama_kurikulum' => 'Kurikulum Merdeka 2024',
        'berlaku_sampai' => 2028,
        'sk_kurikulum' => 'SK-KM-2024',
    ]);

    $mk = MataKuliah::create([
        'id_kurikulum' => $kurikulum->id,
        'kode_mata_kuliah' => 'MK-NILAI-01',
        'nama_mata_kuliah' => 'Basis Data Lanjut',
        'sks' => 3,
    ]);

    $ta = TahunAkademik::create(['tahun' => 2026]);
    $kelas = Kelas::create(['kelas' => 'TRK-3A']);
    $angkatan = Angkatan::create(['angkatan' => 2026]);

    $emk = EnrollmentMataKuliah::create([
        'tahun_akademik_id' => $ta->id,
        'mata_kuliah_id' => $mk->id,
    ]);

    $ak = AngkatanKelas::create([
        'kelas_id' => $kelas->id,
        'angkatan_id' => $angkatan->id,
        'prodi_id' => $prodi->id,
        'tahun_akademik_id' => $ta->id,
    ]);

    return MataKuliahAngkatanKelas::create([
        'enrollment_mata_kuliah_id' => $emk->id,
        'angkatan_kelas_id' => $ak->id,
    ]);
}

test('unauthenticated user cannot access nilai mahasiswa endpoints', function () {
    $this->getJson('/api/nilai-mahasiswa')->assertStatus(401);
});

test('can list all nilai mahasiswa', function () {
    $token = getAuthToken();
    $mkAk = createMataKuliahAngkatanKelas();

    NilaiMahasiswa::create([
        'mata_kuliah_angkatan_kelas_id' => $mkAk->id,
        'link_nilai' => 'https://docs.google.com/spreadsheets/d/123/edit',
        'link_evidence' => 'https://drive.google.com/drive/folders/456',
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/nilai-mahasiswa');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'code' => 200,
            'message' => 'Daftar nilai mahasiswa berhasil diambil',
        ])
        ->assertJsonStructure([
            'success',
            'code',
            'message',
            'data' => [
                '*' => [
                    'id',
                    'mata_kuliah_angkatan_kelas_id',
                    'link_nilai',
                    'link_evidence',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
});

test('can create a new nilai mahasiswa', function () {
    $token = getAuthToken();
    $mkAk = createMataKuliahAngkatanKelas();

    $payload = [
        'mata_kuliah_angkatan_kelas_id' => $mkAk->id,
        'link_nilai' => 'https://docs.google.com/spreadsheets/d/sample-nilai',
        'link_evidence' => 'https://drive.google.com/drive/folders/sample-evidence',
    ];

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/nilai-mahasiswa', $payload);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'code' => 201,
            'message' => 'Nilai mahasiswa berhasil ditambahkan',
            'data' => [
                'mata_kuliah_angkatan_kelas_id' => $mkAk->id,
                'link_nilai' => 'https://docs.google.com/spreadsheets/d/sample-nilai',
                'link_evidence' => 'https://drive.google.com/drive/folders/sample-evidence',
            ],
        ]);

    $this->assertDatabaseHas('nilai_mahasiswa', [
        'mata_kuliah_angkatan_kelas_id' => $mkAk->id,
        'link_nilai' => 'https://docs.google.com/spreadsheets/d/sample-nilai',
        'link_evidence' => 'https://drive.google.com/drive/folders/sample-evidence',
    ]);
});

test('can show single nilai mahasiswa detail', function () {
    $token = getAuthToken();
    $mkAk = createMataKuliahAngkatanKelas();

    $nilai = NilaiMahasiswa::create([
        'mata_kuliah_angkatan_kelas_id' => $mkAk->id,
        'link_nilai' => 'https://docs.google.com/spreadsheets/d/detail-nilai',
        'link_evidence' => 'https://drive.google.com/drive/folders/detail-evidence',
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson("/api/nilai-mahasiswa/{$nilai->id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'code' => 200,
            'message' => 'Detail data nilai mahasiswa berhasil diambil',
            'data' => [
                'id' => $nilai->id,
                'mata_kuliah_angkatan_kelas_id' => $mkAk->id,
                'link_nilai' => 'https://docs.google.com/spreadsheets/d/detail-nilai',
                'link_evidence' => 'https://drive.google.com/drive/folders/detail-evidence',
            ],
        ]);
});

test('can update existing nilai mahasiswa', function () {
    $token = getAuthToken();
    $mkAk = createMataKuliahAngkatanKelas();

    $nilai = NilaiMahasiswa::create([
        'mata_kuliah_angkatan_kelas_id' => $mkAk->id,
        'link_nilai' => 'https://docs.google.com/spreadsheets/d/old-nilai',
        'link_evidence' => 'https://drive.google.com/drive/folders/old-evidence',
    ]);

    $updatePayload = [
        'link_nilai' => 'https://docs.google.com/spreadsheets/d/updated-nilai',
        'link_evidence' => 'https://drive.google.com/drive/folders/updated-evidence',
    ];

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->putJson("/api/nilai-mahasiswa/{$nilai->id}", $updatePayload);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'code' => 200,
            'message' => 'Data nilai mahasiswa berhasil diperbarui',
        ]);

    $this->assertDatabaseHas('nilai_mahasiswa', [
        'id' => $nilai->id,
        'link_nilai' => 'https://docs.google.com/spreadsheets/d/updated-nilai',
        'link_evidence' => 'https://drive.google.com/drive/folders/updated-evidence',
    ]);
});

test('can delete nilai mahasiswa', function () {
    $token = getAuthToken();
    $mkAk = createMataKuliahAngkatanKelas();

    $nilai = NilaiMahasiswa::create([
        'mata_kuliah_angkatan_kelas_id' => $mkAk->id,
        'link_nilai' => 'https://docs.google.com/spreadsheets/d/delete-nilai',
        'link_evidence' => 'https://drive.google.com/drive/folders/delete-evidence',
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->deleteJson("/api/nilai-mahasiswa/{$nilai->id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'code' => 200,
            'message' => 'Data nilai mahasiswa berhasil dihapus',
        ]);

    $this->assertDatabaseMissing('nilai_mahasiswa', [
        'id' => $nilai->id,
    ]);
});

test('can update link_nilai via separate endpoint', function () {
    $token = getAuthToken();
    $mkAk = createMataKuliahAngkatanKelas();

    $nilai = NilaiMahasiswa::create([
        'mata_kuliah_angkatan_kelas_id' => $mkAk->id,
        'link_nilai' => null,
        'link_evidence' => 'https://drive.google.com/drive/folders/existing-evidence',
    ]);

    $payload = [
        'link_nilai' => 'https://docs.google.com/spreadsheets/d/new-link-nilai',
    ];

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->patchJson("/api/nilai-mahasiswa/{$nilai->id}/link-nilai", $payload);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'code' => 200,
            'message' => 'Link nilai mahasiswa berhasil diperbarui',
            'data' => [
                'id' => $nilai->id,
                'link_nilai' => 'https://docs.google.com/spreadsheets/d/new-link-nilai',
                'link_evidence' => 'https://drive.google.com/drive/folders/existing-evidence',
            ],
        ]);

    $this->assertDatabaseHas('nilai_mahasiswa', [
        'id' => $nilai->id,
        'link_nilai' => 'https://docs.google.com/spreadsheets/d/new-link-nilai',
    ]);
});

test('can update link_evidence via separate endpoint', function () {
    $token = getAuthToken();
    $mkAk = createMataKuliahAngkatanKelas();

    $nilai = NilaiMahasiswa::create([
        'mata_kuliah_angkatan_kelas_id' => $mkAk->id,
        'link_nilai' => 'https://docs.google.com/spreadsheets/d/existing-nilai',
        'link_evidence' => null,
    ]);

    $payload = [
        'link_evidence' => 'https://drive.google.com/drive/folders/new-link-evidence',
    ];

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->patchJson("/api/nilai-mahasiswa/{$nilai->id}/link-evidence", $payload);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'code' => 200,
            'message' => 'Link evidence mahasiswa berhasil diperbarui',
            'data' => [
                'id' => $nilai->id,
                'link_nilai' => 'https://docs.google.com/spreadsheets/d/existing-nilai',
                'link_evidence' => 'https://drive.google.com/drive/folders/new-link-evidence',
            ],
        ]);

    $this->assertDatabaseHas('nilai_mahasiswa', [
        'id' => $nilai->id,
        'link_evidence' => 'https://drive.google.com/drive/folders/new-link-evidence',
    ]);
});

test('validates link_nilai on separate update endpoint', function () {
    $token = getAuthToken();
    $mkAk = createMataKuliahAngkatanKelas();

    $nilai = NilaiMahasiswa::create([
        'mata_kuliah_angkatan_kelas_id' => $mkAk->id,
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->patchJson("/api/nilai-mahasiswa/{$nilai->id}/link-nilai", []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['link_nilai']);
});

test('validates link_evidence on separate update endpoint', function () {
    $token = getAuthToken();
    $mkAk = createMataKuliahAngkatanKelas();

    $nilai = NilaiMahasiswa::create([
        'mata_kuliah_angkatan_kelas_id' => $mkAk->id,
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->patchJson("/api/nilai-mahasiswa/{$nilai->id}/link-evidence", []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['link_evidence']);
});

test('returns 404 when updating link of non-existent nilai mahasiswa', function () {
    $token = getAuthToken();

    $responseNilai = $this->withHeader('Authorization', "Bearer {$token}")
        ->patchJson('/api/nilai-mahasiswa/999999/link-nilai', [
            'link_nilai' => 'https://example.com',
        ]);

    $responseNilai->assertStatus(404);

    $responseEvidence = $this->withHeader('Authorization', "Bearer {$token}")
        ->patchJson('/api/nilai-mahasiswa/999999/link-evidence', [
            'link_evidence' => 'https://example.com',
        ]);

    $responseEvidence->assertStatus(404);
});
