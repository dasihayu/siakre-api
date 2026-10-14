<?php

namespace App\Docs;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Role', description: 'Endpoint API Role')]
class RoleDoc
{
    #[OA\Get(
        path: '/api/roles',
        summary: 'Index Role',
        tags: ['Role'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Operasi berhasil'),
            new OA\Response(response: 400, description: 'Permintaan tidak valid'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 404, description: 'Data tidak ditemukan'),
        ]
    )]
    public function index(): void {}

    #[OA\Post(
        path: '/api/roles',
        summary: 'Store Role',
        tags: ['Role'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string'),
                    new OA\Property(property: 'guard_name', type: 'string'),
                    new OA\Property(property: 'permissions', type: 'string'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Operasi berhasil'),
            new OA\Response(response: 400, description: 'Permintaan tidak valid'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 404, description: 'Data tidak ditemukan'),
        ]
    )]
    public function store(): void {}

    #[OA\Get(
        path: '/api/roles/{role}',
        summary: 'Show Role',
        tags: ['Role'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'role',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', example: '1')
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Operasi berhasil'),
            new OA\Response(response: 400, description: 'Permintaan tidak valid'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 404, description: 'Data tidak ditemukan'),
        ]
    )]
    public function show(): void {}

    #[OA\Put(
        path: '/api/roles/{role}',
        summary: 'Update Role',
        tags: ['Role'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'role',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', example: '1')
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string'),
                    new OA\Property(property: 'guard_name', type: 'string'),
                    new OA\Property(property: 'permissions', type: 'string'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Operasi berhasil'),
            new OA\Response(response: 400, description: 'Permintaan tidak valid'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 404, description: 'Data tidak ditemukan'),
        ]
    )]
    public function update(): void {}

    #[OA\Delete(
        path: '/api/roles/{role}',
        summary: 'Destroy Role',
        tags: ['Role'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'role',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', example: '1')
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Operasi berhasil'),
            new OA\Response(response: 400, description: 'Permintaan tidak valid'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 404, description: 'Data tidak ditemukan'),
        ]
    )]
    public function destroy(): void {}
}
