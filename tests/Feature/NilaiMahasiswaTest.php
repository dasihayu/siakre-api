<?php

use App\Models\NilaiMahasiswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Membuat user fiktif dan generate token JWT
    $this->user = User::factory()->create();
    $this->token = auth('api')->login($this->user);
});

test('can list all nilai mahasiswa', function () {
    NilaiMahasiswa::create([
        'enrollment_mahasiswa_angkatan_kelas_id' => 1,
        'mata_kuliah_angkatan_kelas_id' => 1,
        'nilai_angka' => 85.50,
        'nilai_huruf' => 'A',
        'bobot_nilai' => 4.00,
        'status_kelulusan' => 'Lulus',
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$this->token}")
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
                    'enrollment_mahasiswa_angkatan_kelas_id',
                    'mata_kuliah_angkatan_kelas_id',
                    'nilai_angka',
                    'nilai_huruf',
                    'bobot_nilai',
                    'status_kelulusan',
                ],
            ],
        ]);
});

test('can create a new nilai mahasiswa', function () {
    $payload = [
        'enrollment_mahasiswa_angkatan_kelas_id' => 10,
        'mata_kuliah_angkatan_kelas_id' => 5,
        'nilai_angka' => 88.00,
        'nilai_huruf' => 'A',
        'bobot_nilai' => 4.00,
        'status_kelulusan' => 'Lulus',
    ];

    $response = $this->withHeader('Authorization', "Bearer {$this->token}")
        ->postJson('/api/nilai-mahasiswa', $payload);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'code' => 201,
            'message' => 'Nilai mahasiswa berhasil ditambahkan',
        ]);

    $this->assertDatabaseHas('nilai_mahasiswa', [
        'enrollment_mahasiswa_angkatan_kelas_id' => 10,
        'mata_kuliah_angkatan_kelas_id' => 5,
        'nilai_angka' => 88.00,
        'nilai_huruf' => 'A',
    ]);
});

test('can show single nilai mahasiswa detail', function () {
    $nilai = NilaiMahasiswa::create([
        'enrollment_mahasiswa_angkatan_kelas_id' => 1,
        'mata_kuliah_angkatan_kelas_id' => 1,
        'nilai_angka' => 90.00,
        'nilai_huruf' => 'A',
        'bobot_nilai' => 4.00,
        'status_kelulusan' => 'Lulus',
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$this->token}")
        ->getJson("/api/nilai-mahasiswa/{$nilai->id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'code' => 200,
            'message' => 'Detail data nilai mahasiswa berhasil diambil',
            'data' => [
                'id' => $nilai->id,
                'nilai_angka' => 90.00,
                'nilai_huruf' => 'A',
            ],
        ]);
});

test('can update existing nilai mahasiswa', function () {
    $nilai = NilaiMahasiswa::create([
        'enrollment_mahasiswa_angkatan_kelas_id' => 1,
        'mata_kuliah_angkatan_kelas_id' => 1,
        'nilai_angka' => 60.00,
        'nilai_huruf' => 'C',
        'bobot_nilai' => 2.00,
        'status_kelulusan' => 'Lulus',
    ]);

    $updatePayload = [
        'nilai_angka' => 80.00,
        'nilai_huruf' => 'A-',
        'bobot_nilai' => 3.70,
        'status_kelulusan' => 'Lulus',
    ];

    $response = $this->withHeader('Authorization', "Bearer {$this->token}")
        ->putJson("/api/nilai-mahasiswa/{$nilai->id}", $updatePayload);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'code' => 200,
            'message' => 'Data nilai mahasiswa berhasil diperbarui',
        ]);

    $this->assertDatabaseHas('nilai_mahasiswa', [
        'id' => $nilai->id,
        'nilai_angka' => 80.00,
        'nilai_huruf' => 'A-',
    ]);
});

test('can delete nilai mahasiswa', function () {
    $nilai = NilaiMahasiswa::create([
        'enrollment_mahasiswa_angkatan_kelas_id' => 1,
        'mata_kuliah_angkatan_kelas_id' => 1,
        'nilai_angka' => 50.00,
        'nilai_huruf' => 'D',
        'bobot_nilai' => 1.00,
        'status_kelulusan' => 'Tidak Lulus',
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$this->token}")
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