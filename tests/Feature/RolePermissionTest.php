<?php

use App\Enums\UserRole;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);
});

test('user permissions are correctly serialized in JWT token claims', function () {
    /** @var User $user */
    $user = User::factory()->create([
        'email' => 'kaprodi@univ.ac.id',
        'password' => bcrypt('password123'),
        'role' => UserRole::KAPRODI,
    ]);
    $user->assignRole(UserRole::KAPRODI->value);

    $response = $this->postJson('/api/auth/login', [
        'email' => 'kaprodi@univ.ac.id',
        'password' => 'password123',
    ]);

    $response->assertStatus(200);

    expect($response->json('data.authorization.permissions'))->toBeNull();

    $token = $response->json('data.authorization.access_token');
    $payload = JWTAuth::setToken($token)->getPayload();
    $permissions = $payload->get('permissions');

    expect($permissions)->toContain('dosen.create')
        ->toContain('dosen.read')
        ->toContain('dosen.update')
        ->toContain('dosen.delete')
        ->toContain('led.read');
});

test('dosen role has restricted permissions in JWT token claims compared to kaprodi', function () {
    /** @var User $user */
    $user = User::factory()->create([
        'email' => 'dosen@univ.ac.id',
        'password' => bcrypt('password123'),
        'role' => UserRole::DOSEN,
    ]);
    $user->assignRole(UserRole::DOSEN->value);

    $response = $this->postJson('/api/auth/login', [
        'email' => 'dosen@univ.ac.id',
        'password' => 'password123',
    ]);

    expect($response->json('data.authorization.permissions'))->toBeNull();

    $token = $response->json('data.authorization.access_token');
    $payload = JWTAuth::setToken($token)->getPayload();
    $permissions = $payload->get('permissions');

    expect($permissions)->toContain('dosen.read')
        ->toContain('penelitian.create');

    expect($permissions)->not->toContain('dosen.delete')
        ->not->toContain('user.create');
});
