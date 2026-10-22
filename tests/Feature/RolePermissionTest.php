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

test('reviewer role has read only permissions for all master data in JWT token claims', function () {
    /** @var User $user */
    $user = User::factory()->create([
        'email' => 'reviewer@univ.ac.id',
        'password' => bcrypt('password123'),
        'role' => UserRole::REVIEWER,
    ]);
    $user->assignRole(UserRole::REVIEWER->value);

    $response = $this->postJson('/api/auth/login', [
        'email' => 'reviewer@univ.ac.id',
        'password' => 'password123',
    ]);

    expect($response->json('data.authorization.permissions'))->not()->toBeNull();

    $token = $response->json('data.authorization.access_token');
    $payload = JWTAuth::setToken($token)->getPayload();
    $permissions = $payload->get('permissions');

    expect($permissions)->toContain('perguruan_tinggi.read')
        ->toContain('prodi.read')
        ->toContain('kelas.read')
        ->toContain('upps.read')
        ->toContain('anggota_upps.read')
        ->toContain('mata_kuliah.read')
        ->toContain('tahun_akademik.read')
        ->toContain('angkatan.read')
        ->toContain('kurikulum.read')
        ->toContain('cpl.read')
        ->toContain('mata_kuliah_cpl.read')
        ->toContain('mahasiswa.read')
        ->toContain('status_mahasiswa.read')
        ->toContain('kegiatan_mahasiswa.read')
        ->toContain('nilai_mahasiswa.read')
        ->toContain('prestasi_mahasiswa.read')
        ->toContain('evidence_prestasi_mahasiswa.read')
        ->toContain('jenis_prestasi.read')
        ->toContain('angkatan_kelas.read')
        ->toContain('enrollment_kelas.read')
        ->toContain('enrollment_mata_kuliah.read')
        ->toContain('mata_kuliah_angkatan_kelas.read')
        ->toContain('dosen.read');

    // Reviewer must NOT have create/update/delete permissions
    expect($permissions)->not->toContain('kurikulum.create')
        ->not->toContain('kurikulum.update')
        ->not->toContain('kurikulum.delete')
        ->not->toContain('nilai_mahasiswa.create')
        ->not->toContain('user.read')
        ->not->toContain('user.create')
        ->not->toContain('role.create');
});

test('system only defines exactly 4 roles', function () {
    $roles = array_map(fn ($case) => $case->value, UserRole::cases());

    expect($roles)->toBe([
        'ADMINISTRATOR',
        'DOSEN',
        'KAPRODI',
        'REVIEWER',
    ]);
});

test('jwt payload contains standard sso claims matching k2 specification', function () {
    $this->seed(\Database\Seeders\UserSeeder::class);

    $response = $this->postJson('/api/auth/login', [
        'email' => 'dosen@example.com',
        'password' => 'password',
    ]);

    $response->assertStatus(200);

    $token = $response->json('data.authorization.access_token');
    $payload = JWTAuth::setToken($token)->getPayload();

    expect($payload->get('email'))->toBe('dosen@example.com')
        ->and($payload->get('nama'))->toBe('Dr. Dosen Contoh, M.Kom')
        ->and($payload->get('role'))->toBe('DOSEN')
        ->and($payload->get('permissions'))->toBeArray()
        ->and($payload->get('permissions'))->toContain('dosen.read')
        ->toContain('penelitian.create')
        ->toContain('pengabdian.create')
        ->toContain('kegiatan.create')
        ->toContain('luaran.create')
        ->toContain('sertifikasi.create')
        ->toContain('evidence.create')
        ->toContain('dashboard.read');
});

test('administrator account receives wildcard permission in jwt token', function () {
    $this->seed(\Database\Seeders\UserSeeder::class);

    $response = $this->postJson('/api/auth/login', [
        'email' => 'administrator@example.com',
        'password' => 'password',
    ]);

    $response->assertStatus(200);

    $token = $response->json('data.authorization.access_token');
    $payload = JWTAuth::setToken($token)->getPayload();

    expect($payload->get('role'))->toBe('ADMINISTRATOR')
        ->and($payload->get('permissions'))->toBe(['*']);
});

test('operator account receives restricted direct permissions in jwt token', function () {
    $this->seed(\Database\Seeders\UserSeeder::class);

    $response = $this->postJson('/api/auth/login', [
        'email' => 'operator@example.com',
        'password' => 'password',
    ]);

    $response->assertStatus(200);

    $token = $response->json('data.authorization.access_token');
    $payload = JWTAuth::setToken($token)->getPayload();

    expect($payload->get('role'))->toBe('ADMINISTRATOR')
        ->and($payload->get('permissions'))->toContain('dosen.*')
        ->toContain('tendik.*')
        ->toContain('mitra.*')
        ->toContain('atribut.*')
        ->toContain('kegiatan.*')
        ->toContain('luaran.*')
        ->toContain('sertifikasi.*')
        ->toContain('kerjasama.*')
        ->toContain('evidence.*')
        ->toContain('dashboard.read')
        ->not->toContain('*');
});

test('kaprodi and reviewer receive k2 permissions including evidence validate and assessment run', function () {
    $this->seed(\Database\Seeders\UserSeeder::class);

    // Kaprodi
    $responseKaprodi = $this->postJson('/api/auth/login', [
        'email' => 'kaprodi@example.com',
        'password' => 'password',
    ]);
    $tokenKaprodi = $responseKaprodi->json('data.authorization.access_token');
    $payloadKaprodi = JWTAuth::setToken($tokenKaprodi)->getPayload();
    $kaprodiPermissions = $payloadKaprodi->get('permissions');

    expect($kaprodiPermissions)->toContain('evidence.validate')
        ->toContain('assessment.run')
        ->toContain('dashboard.read')
        ->toContain('led.read')
        ->toContain('lkps.read');

    // Reviewer
    $responseReviewer = $this->postJson('/api/auth/login', [
        'email' => 'reviewer@example.com',
        'password' => 'password',
    ]);
    $tokenReviewer = $responseReviewer->json('data.authorization.access_token');
    $payloadReviewer = JWTAuth::setToken($tokenReviewer)->getPayload();
    $reviewerPermissions = $payloadReviewer->get('permissions');

    expect($reviewerPermissions)->toContain('evidence.validate')
        ->toContain('assessment.run')
        ->toContain('dashboard.read')
        ->toContain('led.read')
        ->toContain('lkps.read');

    // Pimpinan (no evidence.validate)
    $responsePimpinan = $this->postJson('/api/auth/login', [
        'email' => 'pimpinan@example.com',
        'password' => 'password',
    ]);
    $tokenPimpinan = $responsePimpinan->json('data.authorization.access_token');
    $payloadPimpinan = JWTAuth::setToken($tokenPimpinan)->getPayload();
    $pimpinanPermissions = $payloadPimpinan->get('permissions');

    expect($pimpinanPermissions)->toContain('assessment.run')
        ->toContain('dashboard.read')
        ->not->toContain('evidence.validate');
});
