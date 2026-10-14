<?php

namespace App\Docs;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Perguruan Tinggi', description: 'Endpoint API Perguruan Tinggi')]
class PerguruanTinggiDoc
{
    #[OA\Get(
        path: '/api/perguruan-tinggi',
        summary: 'Index Perguruan Tinggi',
        tags: ['Perguruan Tinggi'],
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
        path: '/api/perguruan-tinggi',
        summary: 'Store Perguruan Tinggi',
        tags: ['Perguruan Tinggi'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Operasi berhasil'),
            new OA\Response(response: 400, description: 'Permintaan tidak valid'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 404, description: 'Data tidak ditemukan'),
        ]
    )]
    public function store(): void {}

    #[OA\Get(
        path: '/api/perguruan-tinggi/{id}',
        summary: 'Show Perguruan Tinggi',
        tags: ['Perguruan Tinggi'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
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
        path: '/api/perguruan-tinggi/{id}',
        summary: 'Update Perguruan Tinggi',
        tags: ['Perguruan Tinggi'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
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
    public function update(): void {}

    #[OA\Delete(
        path: '/api/perguruan-tinggi/{id}',
        summary: 'Destroy Perguruan Tinggi',
        tags: ['Perguruan Tinggi'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
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
