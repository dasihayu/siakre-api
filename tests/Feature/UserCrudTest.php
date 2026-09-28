<?php

use App\Enums\UserRole;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);
});

test('administrator can list users with pagination and standard response format', function () {
    /** @var User $admin */
    $admin = User::factory()->create(['role' => UserRole::ADMINISTRATOR]);
    $admin->assignRole(UserRole::ADMINISTRATOR->value);
    $token = auth('api')->login($admin);

    User::factory()->count(5)->create();

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/users');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'code',
            'message',
            'data' => [
                '*' => ['id', 'nama', 'email', 'role', 'created_at', 'updated_at'],
            ],
            'pagination' => [
                'total',
                'per_page',
                'current_page',
                'last_page',
                'next_page_url',
                'prev_page_url',
            ],
        ])
        ->assertJson([
            'success' => true,
            'code' => 200,
        ]);
});

test('administrator can create a new user', function () {
    /** @var User $admin */
    $admin = User::factory()->create(['role' => UserRole::ADMINISTRATOR]);
    $admin->assignRole(UserRole::ADMINISTRATOR->value);
    $token = auth('api')->login($admin);

    $userData = [
        'nama' => 'Dr. Andi Wijaya',
        'email' => 'andi@univ.ac.id',
        'password' => 'password123',
        'role' => UserRole::KAPRODI->value,
    ];

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/users', $userData);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'code' => 201,
            'data' => [
                'nama' => 'Dr. Andi Wijaya',
                'email' => 'andi@univ.ac.id',
                'role' => 'KAPRODI',
            ],
        ]);

    $this->assertDatabaseHas('users', ['email' => 'andi@univ.ac.id']);
});

test('administrator can view user detail', function () {
    /** @var User $admin */
    $admin = User::factory()->create(['role' => UserRole::ADMINISTRATOR]);
    $admin->assignRole(UserRole::ADMINISTRATOR->value);
    $token = auth('api')->login($admin);

    $targetUser = User::factory()->create(['name' => 'Siti Aminah']);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson("/api/users/{$targetUser->id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'code' => 200,
            'data' => [
                'id' => $targetUser->id,
                'nama' => 'Siti Aminah',
            ],
        ]);
});

test('administrator can update a user', function () {
    /** @var User $admin */
    $admin = User::factory()->create(['role' => UserRole::ADMINISTRATOR]);
    $admin->assignRole(UserRole::ADMINISTRATOR->value);
    $token = auth('api')->login($admin);

    $targetUser = User::factory()->create(['role' => UserRole::DOSEN]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->putJson("/api/users/{$targetUser->id}", [
            'nama' => 'Dr. Siti Aminah, M.Kom',
            'role' => UserRole::KAPRODI->value,
        ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'code' => 200,
            'data' => [
                'id' => $targetUser->id,
                'nama' => 'Dr. Siti Aminah, M.Kom',
                'role' => 'KAPRODI',
            ],
        ]);
});

test('administrator can delete a user', function () {
    /** @var User $admin */
    $admin = User::factory()->create(['role' => UserRole::ADMINISTRATOR]);
    $admin->assignRole(UserRole::ADMINISTRATOR->value);
    $token = auth('api')->login($admin);

    $targetUser = User::factory()->create();

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->deleteJson("/api/users/{$targetUser->id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'code' => 200,
            'data' => null,
        ]);

    $this->assertDatabaseMissing('users', ['id' => $targetUser->id]);
});

test('non-administrator role cannot access user CRUD routes', function () {
    /** @var User $kaprodi */
    $kaprodi = User::factory()->create(['role' => UserRole::KAPRODI]);
    $kaprodi->assignRole(UserRole::KAPRODI->value);
    $token = auth('api')->login($kaprodi);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/users');

    $response->assertStatus(403)
        ->assertJson([
            'success' => false,
            'code' => 403,
            'data' => null,
        ]);
});

test('unauthenticated request is rejected with 401', function () {
    $response = $this->getJson('/api/users');

    $response->assertStatus(401)
        ->assertJson([
            'success' => false,
            'code' => 401,
            'data' => null,
        ]);
});

test('unauthenticated request without accept json header returns 401 json response', function () {
    $response = $this->get('/api/users');

    $response->assertStatus(401)
        ->assertJson([
            'success' => false,
            'code' => 401,
            'message' => 'Unauthenticated.',
        ]);
});
