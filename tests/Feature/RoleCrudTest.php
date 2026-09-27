<?php

use App\Enums\UserRole;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);

    $this->admin = User::factory()->create([
        'email' => 'admin@univ.ac.id',
        'password' => bcrypt('password123'),
        'role' => UserRole::ADMINISTRATOR,
    ]);
    $this->admin->assignRole(UserRole::ADMINISTRATOR->value);
});

test('admin can list roles', function () {
    $response = $this->actingAs($this->admin, 'api')
        ->getJson('/api/roles');

    $response->assertStatus(200)
        ->assertJsonPath('success', true)
        ->assertJsonStructure([
            'success',
            'code',
            'message',
            'data' => [
                '*' => ['id', 'name', 'guard_name', 'permissions'],
            ],
            'pagination',
        ]);
});

test('admin can create a new role with permissions', function () {
    $response = $this->actingAs($this->admin, 'api')
        ->postJson('/api/roles', [
            'name' => 'SUPERVISOR',
            'permissions' => ['dosen.read', 'kriteria.read'],
        ]);

    $response->assertStatus(201)
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.name', 'SUPERVISOR');

    $this->assertDatabaseHas('roles', [
        'name' => 'SUPERVISOR',
        'guard_name' => 'api',
    ]);

    $role = Role::findByName('SUPERVISOR', 'api');
    expect($role->hasPermissionTo('dosen.read'))->toBeTrue()
        ->and($role->hasPermissionTo('kriteria.read'))->toBeTrue();
});

test('admin can show role detail', function () {
    $role = Role::findByName(UserRole::KAPRODI->value, 'api');

    $response = $this->actingAs($this->admin, 'api')
        ->getJson("/api/roles/{$role->id}");

    $response->assertStatus(200)
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.name', UserRole::KAPRODI->value);
});

test('admin can update role and sync permissions', function () {
    $role = Role::create(['name' => 'TEMP_ROLE', 'guard_name' => 'api']);

    $response = $this->actingAs($this->admin, 'api')
        ->putJson("/api/roles/{$role->id}", [
            'name' => 'UPDATED_ROLE',
            'permissions' => ['user.read'],
        ]);

    $response->assertStatus(200)
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.name', 'UPDATED_ROLE');

    $this->assertDatabaseHas('roles', ['name' => 'UPDATED_ROLE']);
});

test('admin can delete a role', function () {
    $role = Role::create(['name' => 'TO_DELETE', 'guard_name' => 'api']);

    $response = $this->actingAs($this->admin, 'api')
        ->deleteJson("/api/roles/{$role->id}");

    $response->assertStatus(200)
        ->assertJsonPath('success', true);

    $this->assertDatabaseMissing('roles', ['name' => 'TO_DELETE']);
});
