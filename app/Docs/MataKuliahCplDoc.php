<?php

namespace App\Docs;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Mata Kuliah Cpl', description: 'Endpoint API Mata Kuliah Cpl')]
class MataKuliahCplDoc
{
    #[OA\Get(
        path: '/api/mata-kuliah/{mataKuliahId}/cpl',
        summary: 'Index Mata Kuliah Cpl',
        tags: ['Mata Kuliah Cpl'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'mataKuliahId',
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
    public function index(): void {}

    #[OA\Post(
        path: '/api/mata-kuliah/{mataKuliahId}/cpl/sync',
        summary: 'Sync Mata Kuliah Cpl',
        tags: ['Mata Kuliah Cpl'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'mataKuliahId',
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
    public function sync(): void {}
}
