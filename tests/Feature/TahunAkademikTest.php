<?php

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
            'data'
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