<?php

use App\Models\Angkatan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated user can list and create angkatan', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    Angkatan::create(['angkatan' => 2024]);

    // Test GET all angkatan
    $responseGet = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/angkatan');

    $responseGet->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'code',
            'message',
            'data'
        ]);

    // Test POST create angkatan
    $payload = ['angkatan' => 2026];

    $responsePost = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/angkatan', $payload);

    $responsePost->assertStatus(201)
        ->assertJson([
            'success' => true,
            'message' => 'Angkatan berhasil ditambahkan',
        ]);

    // Assert database has the newly created angkatan
    $this->assertDatabaseHas('angkatan', [
        'angkatan' => 2026,
    ]);
});
test('authenticated user can update and delete angkatan', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $angkatan = Angkatan::create(['angkatan' => 2021]);

    // Test PUT update angkatan
    $responsePut = $this->withHeader('Authorization', "Bearer {$token}")
        ->putJson("/api/angkatan/{$angkatan->id}", ['angkatan' => 2022]);

    $responsePut->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Angkatan berhasil diperbarui',
        ]);

    $this->assertDatabaseHas('angkatan', ['id' => $angkatan->id, 'angkatan' => 2022]);

    // Test DELETE angkatan
    $responseDelete = $this->withHeader('Authorization', "Bearer {$token}")
        ->deleteJson("/api/angkatan/{$angkatan->id}");

    $responseDelete->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Angkatan berhasil dihapus',
        ]);

    $this->assertDatabaseMissing('angkatan', ['id' => $angkatan->id]);
});
