<?php

namespace App\Docs;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Mata Kuliah', description: 'Endpoint API Mata Kuliah')]
class MataKuliahDoc
{
    #[OA\Get(
        path: '/api/mata-kuliah',
        summary: 'Index Mata Kuliah',
        tags: ['Mata Kuliah'],
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
        path: '/api/mata-kuliah',
        summary: 'Store Mata Kuliah',
        tags: ['Mata Kuliah'],
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
        path: '/api/mata-kuliah/{id}',
        summary: 'Show Mata Kuliah',
        tags: ['Mata Kuliah'],
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
        path: '/api/mata-kuliah/{id}',
        summary: 'Update Mata Kuliah',
        tags: ['Mata Kuliah'],
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
        path: '/api/mata-kuliah/{id}',
        summary: 'Destroy Mata Kuliah',
        tags: ['Mata Kuliah'],
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
