<?php

use App\Enums\UserRole;
use App\Helpers\ApiResponse;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('auth response structure matches standard format', function () {
    $user = new User([
        'id' => 15,
        'name' => 'Prof. Dr. Ir. H. Budi Santoso',
        'email' => 'budi@univ.ac.id',
        'role' => UserRole::KAPRODI,
    ]);
    $user->id = 15;

    $response = ApiResponse::auth(
        user: $user,
        token: 'sample-jwt-token',
        permissions: ['read_dosen', 'create_penelitian', 'update_penelitian'],
        message: 'Login successful'
    );

    expect($response->getStatusCode())->toBe(200);

    $json = $response->getData(true);
    expect($json)->toBe([
        'success' => true,
        'code' => 200,
        'message' => 'Login successful',
        'data' => [
            'user' => [
                'id' => 15,
                'nama' => 'Prof. Dr. Ir. H. Budi Santoso',
                'email' => 'budi@univ.ac.id',
                'role' => 'KAPRODI',
            ],
            'authorization' => [
                'access_token' => 'sample-jwt-token',
                'token_type' => 'Bearer',
                'expires_in' => 3600,
                'permissions' => [
                    'read_dosen',
                    'create_penelitian',
                    'update_penelitian',
                ],
            ],
        ],
    ]);
});

test('single data response structure matches standard format', function () {
    $data = [
        'id' => 1,
        'kode_kriteria' => 'C1',
        'nama' => 'Visi, Misi, Tujuan, dan Strategi',
        'bobot' => 5,
    ];

    $response = ApiResponse::success($data, 'Criteria data retrieved successfully');

    expect($response->getStatusCode())->toBe(200);
    $json = $response->getData(true);

    expect($json)->toBe([
        'success' => true,
        'code' => 200,
        'message' => 'Criteria data retrieved successfully',
        'data' => $data,
    ]);
});

test('paginated data response structure matches standard format', function () {
    $items = collect([
        ['id' => 1, 'nama' => 'Dr. Budi Santoso', 'nidn' => '123456789'],
        ['id' => 2, 'nama' => 'Siti Aminah, M.Kom', 'nidn' => '987654321'],
    ]);

    $paginator = new LengthAwarePaginator(
        $items,
        50,
        10,
        1,
        ['path' => 'http://api-2.local/api/dosen']
    );

    $response = ApiResponse::success($paginator, 'Lecturer list loaded successfully');

    expect($response->getStatusCode())->toBe(200);
    $json = $response->getData(true);

    expect($json['success'])->toBeTrue();
    expect($json['code'])->toBe(200);
    expect($json['message'])->toBe('Lecturer list loaded successfully');
    expect($json['data'])->toHaveCount(2);
    expect($json['pagination'])->toBe([
        'total' => 50,
        'per_page' => 10,
        'current_page' => 1,
        'last_page' => 5,
        'next_page_url' => 'http://api-2.local/api/dosen?page=2',
        'prev_page_url' => null,
    ]);
});

test('validation error response structure matches standard format', function () {
    $errors = [
        'nidn' => [
            'The NIDN must be 10 digits.',
            'The NIDN has already been taken.',
        ],
        'email' => [
            'The email field format is invalid.',
        ],
    ];

    $response = ApiResponse::validationError($errors);

    expect($response->getStatusCode())->toBe(422);
    $json = $response->getData(true);

    expect($json)->toBe([
        'success' => false,
        'code' => 422,
        'message' => 'Validation failed',
        'errors' => $errors,
        'data' => null,
    ]);
});

test('general error response structure matches standard format', function () {
    $response = ApiResponse::error('You do not have permission to delete lecturer data', 403);

    expect($response->getStatusCode())->toBe(403);
    $json = $response->getData(true);

    expect($json)->toBe([
        'success' => false,
        'code' => 403,
        'message' => 'You do not have permission to delete lecturer data',
        'errors' => null,
        'data' => null,
    ]);
});
