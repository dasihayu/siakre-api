<?php

use App\Enums\UserRole;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;

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

test('admin can list permissions', function () {
    $response = $this->actingAs($this->admin, 'api')
        ->getJson('/api/permissions');

    $response->assertStatus(200)
        ->assertJsonPath('success', true)
        ->assertJsonStructure([
            'success',
            'code',
            'message',
            'data' => [
                '*' => ['id', 'name', 'guard_name'],
            ],
            'pagination',
        ]);
});

test('admin can create a new permission', function () {
    $response = $this->actingAs($this->admin, 'api')
        ->postJson('/api/permissions', [
            'name' => 'report.export',
        ]);

    $response->assertStatus(201)
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.name', 'report.export');

    $this->assertDatabaseHas('permissions', [
        'name' => 'report.export',
        'guard_name' => 'api',
    ]);
});

test('admin can show permission detail', function () {
    $permission = Permission::findByName('dosen.read', 'api');

    $response = $this->actingAs($this->admin, 'api')
        ->getJson("/api/permissions/{$permission->id}");

    $response->assertStatus(200)
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.name', 'dosen.read');
});

test('admin can update a permission', function () {
    $permission = Permission::create(['name' => 'temp.permission', 'guard_name' => 'api']);

    $response = $this->actingAs($this->admin, 'api')
        ->putJson("/api/permissions/{$permission->id}", [
            'name' => 'updated.permission',
        ]);

    $response->assertStatus(200)
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.name', 'updated.permission');

    $this->assertDatabaseHas('permissions', ['name' => 'updated.permission']);
});

test('admin can delete a permission', function () {
    $permission = Permission::create(['name' => 'to.delete', 'guard_name' => 'api']);

    $response = $this->actingAs($this->admin, 'api')
        ->deleteJson("/api/permissions/{$permission->id}");

    $response->assertStatus(200)
        ->assertJsonPath('success', true);

    $this->assertDatabaseMissing('permissions', ['name' => 'to.delete']);
});
