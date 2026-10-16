<?php

use App\Models\Kelas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated user can list and create kelas', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    Kelas::create(['kelas' => 'TI-1A']);

    // Test GET all kelas
    $responseGet = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/kelas');

    $responseGet->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'code',
            'message',
            'data'
        ]);

    // Test POST create kelas
    $payload = ['kelas' => 'TI-2A'];

    $responsePost = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/kelas', $payload);

    $responsePost->assertStatus(201)
        ->assertJson([
            'success' => true,
            'message' => 'Kelas berhasil ditambahkan',
        ]);

    // Assert database has the newly created kelas after the POST request
    $this->assertDatabaseHas('kelas', [
        'kelas' => 'TI-2A',
    ]);
});