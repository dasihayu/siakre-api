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
                ],
            ],
        ])
        ->assertJson([
            'success' => true,
            'code' => 200,
            'message' => 'Login berhasil',
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
            'message' => 'Email atau password salah.',
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
            'message' => 'Validasi input gagal',
            'data' => null,
        ]);
});

test('new user can login and register via google with polines email', function () {
    $response = $this->postJson('/api/auth/google', [
        'google_id' => 'google-uid-12345',
        'email' => 'john.doe@polines.ac.id',
        'name' => 'John Doe',
        'avatar' => 'https://lh3.googleusercontent.com/a/avatar.jpg',
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
            'message' => 'Login Google berhasil',
            'data' => [
                'user' => [
                    'nama' => 'John Doe',
                    'email' => 'john.doe@polines.ac.id',
                    'role' => UserRole::DOSEN->value,
                ],
            ],
        ]);

    $this->assertDatabaseHas('users', [
        'email' => 'john.doe@polines.ac.id',
        'google_id' => 'google-uid-12345',
        'avatar' => 'https://lh3.googleusercontent.com/a/avatar.jpg',
        'role' => UserRole::DOSEN->value,
    ]);
});

test('new user can login via google with subdomain of polines', function () {
    $response = $this->postJson('/api/auth/google', [
        'google_id' => 'google-uid-subdomain',
        'email' => 'student@students.polines.ac.id',
        'name' => 'Student Polines',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'code' => 200,
            'message' => 'Login Google berhasil',
        ]);
});

test('google login rejects non-polines email domain', function () {
    $response = $this->postJson('/api/auth/google', [
        'google_id' => 'google-uid-gmail',
        'email' => 'someone@gmail.com',
        'name' => 'Someone',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

test('google login rejects fake polines email domain', function () {
    $response = $this->postJson('/api/auth/google', [
        'google_id' => 'google-uid-fake',
        'email' => 'hacker@fake-polines.ac.id',
        'name' => 'Hacker',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

test('existing user can link and login via google with polines email', function () {
    $user = User::factory()->create([
        'email' => 'existing.user@polines.ac.id',
        'name' => 'Existing User',
        'role' => UserRole::KAPRODI,
        'google_id' => null,
    ]);

    $response = $this->postJson('/api/auth/google', [
        'google_id' => 'google-uid-99999',
        'email' => 'existing.user@polines.ac.id',
        'name' => 'Existing User',
        'avatar' => 'https://lh3.googleusercontent.com/avatar2.jpg',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'code' => 200,
            'message' => 'Login Google berhasil',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'email' => 'existing.user@polines.ac.id',
                    'role' => UserRole::KAPRODI->value,
                ],
            ],
        ]);

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'google_id' => 'google-uid-99999',
        'avatar' => 'https://lh3.googleusercontent.com/avatar2.jpg',
    ]);
});

test('google login requires mandatory fields', function () {
    $response = $this->postJson('/api/auth/google', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['google_id', 'email', 'name']);
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
            'message' => 'Profil pengguna berhasil diambil',
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
            'message' => 'Berhasil logout',
        ]);
});
