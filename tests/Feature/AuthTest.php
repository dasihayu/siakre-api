<?php

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user can login with correct credentials', function () {
    User::factory()->create([
        'email' => 'jane@example.com',
        'password' => bcrypt('password123'),
        'role' => UserRole::KAPRODI,
    ]);

    $response = $this->postJson('/api/auth/login', [
        'email' => 'jane@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'code',
            'message',
            'data' => [
                'user' => [
                    'id',
                    'nama',
                    'email',
                    'role',
                ],
                'authorization' => [
                    'access_token',
                    'token_type',
                    'expires_in',
                    'permissions',
                ],
            ],
        ])
        ->assertJson([
            'success' => true,
            'code' => 200,
            'message' => 'Login successful',
        ]);
});

test('user cannot login with invalid credentials', function () {
    User::factory()->create([
        'email' => 'jane@example.com',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->postJson('/api/auth/login', [
        'email' => 'jane@example.com',
        'password' => 'wrong-password',
    ]);

    $response->assertStatus(401)
        ->assertJson([
            'success' => false,
            'code' => 401,
            'message' => 'Invalid email or password.',
            'errors' => null,
            'data' => null,
        ]);
});

test('validation error returns standard 422 error structure', function () {
    $response = $this->postJson('/api/auth/login', [
        'email' => 'not-an-email',
    ]);

    $response->assertStatus(422)
        ->assertJsonStructure([
            'success',
            'code',
            'message',
            'errors',
            'data',
        ])
        ->assertJson([
            'success' => false,
            'code' => 422,
            'message' => 'Input validation failed',
            'data' => null,
        ]);
});

test('authenticated user can fetch profile', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/auth/me');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'code' => 200,
            'message' => 'User profile retrieved successfully',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                ],
            ],
        ]);
});

test('authenticated user can logout', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/auth/logout');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'code' => 200,
            'message' => 'Successfully logged out',
        ]);
});
