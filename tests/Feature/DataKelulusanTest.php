<?php

use App\Models\Mahasiswa;
use App\Models\DataKelulusan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated user can create and list data kelulusan', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $mhs = Mahasiswa::create([
        'nim'                    => '3.34.24.1.99',
        'nama_mahasiswa'         => 'Wisudawan Test',
        'tahun_masuk'            => 2020,
        'jenis_pendaftaran'      => 'Reguler',
        'mahasiswa_internasional'=> false,
    ]);

    $payload = [
        'id_mahasiswa'      => $mhs->id,
        'tanggal_kelulusan' => '2026-09-13',
        'ipk'               => 3.88,
        'masa_studi_bulan'  => 48,
    ];

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/data-kelulusan', $payload);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'message' => 'Data kelulusan berhasil ditambahkan',
        ]);

    $this->assertDatabaseHas('data_kelulusan', [
        'id_mahasiswa' => $mhs->id,
        'ipk' => 3.88,
    ]);
});