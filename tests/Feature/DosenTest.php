<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

test('unauthenticated user cannot access dosen endpoint', function () {
    $this->getJson('/api/dosen')->assertStatus(401);
});

test('authenticated user can fetch list of dosen from tridharma service', function () {
    $user = User::factory()->create();
    $token = auth('api')->login($user);

    $fakeDosenData = [
        'success' => true,
        'data' => [
            'current_page' => 1,
            'data' => [
                [
                    'id_dosen' => 2,
                    'nidn' => '0015088501',
                    'nama_dosen' => 'Dr. Ir. Ahmad Dahlan, M.T.',
                    'email' => 'ahmad.dahlan@polines.ac.id',
                    'status_dosen' => 'DTPS',
                ],
                [
                    'id_dosen' => 3,
                    'nidn' => '666',
                    'nama_dosen' => 'Ahmad Lucifer',
                    'email' => 'nasipadang.enk@polines.ac.id',
                    'status_dosen' => 'DTPS',
                ],
            ],
            'total' => 2,
        ],
    ];

    Http::fake([
        'https://tridharma.fayra.my.id/api/dosen*' => Http::response($fakeDosenData, 200),
    ]);

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/dosen');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'data' => [
                'total' => 2,
            ],
        ])
        ->assertJsonPath('data.data.0.id_dosen', 2)
        ->assertJsonPath('data.data.0.nama_dosen', 'Dr. Ir. Ahmad Dahlan, M.T.');
});
