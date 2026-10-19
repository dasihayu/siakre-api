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

    expect($response->json('data.authorization.permissions'))->not()->toBeNull();

    $token = $response->json('data.authorization.access_token');
    $payload = JWTAuth::setToken($token)->getPayload();
    $permissions = $payload->get('permissions');

    expect($permissions)->toContain('kurikulum.create')
        ->toContain('kurikulum.read')
        ->toContain('kurikulum.update')
        ->toContain('kurikulum.delete')
        ->toContain('cpl.read');
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

    expect($response->json('data.authorization.permissions'))->not()->toBeNull();

    $token = $response->json('data.authorization.access_token');
    $payload = JWTAuth::setToken($token)->getPayload();
    $permissions = $payload->get('permissions');

    expect($permissions)->toContain('nilai_mahasiswa.create')
        ->toContain('kurikulum.read');

    expect($permissions)->not->toContain('kurikulum.delete')
        ->not->toContain('user.create');
});
