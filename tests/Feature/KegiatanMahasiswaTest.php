<?php

use App\Models\Mahasiswa;
use App\Models\TahunAkademik;
use App\Models\KegiatanMahasiswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('unauthenticated user cannot access kegiatan mahasiswa endpoints', function () {
    $this->getJson('/api/kegiatan-mahasiswa')->assertStatus(401);
});

test('authenticated user can list kegiatan mahasiswa', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $mhs = Mahasiswa::create([
        'nim'               => 33424101,
        'nama_mahasiswa'    => 'Andrian',
        'tahun_masuk'       => 2024,
        'jenis_pendaftaran' => 'Reguler',
        'masa_studi_bulan'  => 48,
    ]);

    $ta = TahunAkademik::create([
        'tahun' => '2024'
    ]);
    
    KegiatanMahasiswa::create([
        'mahasiswa_id'      => $mhs->id,
        'tahun_akademik_id' => $ta->id,
        'nama_kegiatan'     => 'Lomba Gemastik',
        'jenis_kegiatan'    => 'Lomba',
        'peran'             => 'Peserta',
        'tingkat'           => 'Nasional',
        'tanggal_mulai'     => '2025-05-10',
        'penyelenggara'     => 'Puspresnas',
        'status_validasi'   => 'Pending',
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/kegiatan-mahasiswa');

    $response->assertStatus(200)
        ->assertJson(['success' => true]);

    expect($response->json('data'))->toHaveCount(1);
});

test('authenticated user can create kegiatan mahasiswa', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $mhs = Mahasiswa::create([
        'nim'               => 33424102,
        'nama_mahasiswa'    => 'Budi',
        'tahun_masuk'       => 2024,
        'jenis_pendaftaran' => 'Reguler',
        'masa_studi_bulan'  => 48,
    ]);

    $ta = TahunAkademik::create([
        'tahun' => '2024'
    ]);

    $payload = [
        'mahasiswa_id'      => $mhs->id,
        'tahun_akademik_id' => $ta->id,
        'nama_kegiatan'     => 'Seminar IT',
        'jenis_kegiatan'    => 'Seminar',
        'peran'             => 'Panitia',
        'tingkat'           => 'Fakultas',
        'tanggal_mulai'     => '2025-08-01',
        'tanggal_selesai'   => '2025-08-02',
        'penyelenggara'     => 'Himpunan Mahasiswa',
        'status_validasi'   => 'Disetujui',
    ];

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/kegiatan-mahasiswa', $payload);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'message' => 'Data kegiatan mahasiswa berhasil ditambahkan',
        ]);

    $this->assertDatabaseHas('kegiatan_mahasiswa', [
        'nama_kegiatan' => 'Seminar IT',
        'peran'         => 'Panitia',
    ]);
});

test('authenticated user can view detail, update, and delete kegiatan mahasiswa', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $mhs = Mahasiswa::create([
        'nim'               => 33424103,
        'nama_mahasiswa'    => 'Citra',
        'tahun_masuk'       => 2023,
        'jenis_pendaftaran' => 'Reguler',
        'masa_studi_bulan'  => 48,
    ]);

    $ta = TahunAkademik::create([
        'tahun' => '2025'
    ]);

    $kegiatan = KegiatanMahasiswa::create([
        'mahasiswa_id'      => $mhs->id,
        'tahun_akademik_id' => $ta->id,
        'nama_kegiatan'     => 'Lomba A',
        'jenis_kegiatan'    => 'Lomba',
        'peran'             => 'Peserta',
        'tingkat'           => 'Lokal',
        'tanggal_mulai'     => '2025-10-01',
        'penyelenggara'     => 'Kampus',
        'status_validasi'   => 'Pending',
    ]);

    // View
    $this->withHeader('Authorization', "Bearer {$token}")
         ->getJson("/api/kegiatan-mahasiswa/{$kegiatan->id}")
         ->assertStatus(200)
         ->assertJsonPath('data.nama_kegiatan', 'Lomba A');

    // Update
    $this->withHeader('Authorization', "Bearer {$token}")
         ->putJson("/api/kegiatan-mahasiswa/{$kegiatan->id}", [
            'mahasiswa_id'      => $mhs->id,
            'tahun_akademik_id' => $ta->id,
            'nama_kegiatan'     => 'Lomba A Update',
            'jenis_kegiatan'    => 'Lomba',
            'peran'             => 'Juara 1',
            'tingkat'           => 'Lokal',
            'tanggal_mulai'     => '2025-10-01',
            'penyelenggara'     => 'Kampus',
            'status_validasi'   => 'Disetujui',
         ])
         ->assertStatus(200);

    $this->assertDatabaseHas('kegiatan_mahasiswa', [
        'nama_kegiatan' => 'Lomba A Update',
        'status_validasi' => 'Disetujui'
    ]);

    // Delete
    $this->withHeader('Authorization', "Bearer {$token}")
         ->deleteJson("/api/kegiatan-mahasiswa/{$kegiatan->id}")
         ->assertStatus(200);

    $this->assertDatabaseMissing('kegiatan_mahasiswa', [
        'id' => $kegiatan->id,
    ]);
});