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
