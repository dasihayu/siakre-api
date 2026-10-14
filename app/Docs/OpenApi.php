<?php

namespace App\Docs;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Akreditasi API',
    description: 'Dokumentasi REST API Sistem Informasi Akreditasi',
    contact: new OA\Contact(email: 'admin@akreditasi.ac.id')
)]
#[OA\Server(
    url: L5_SWAGGER_CONST_HOST,
    description: 'API Server'
)]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'JWT',
    description: 'Masukkan JWT token Anda. Contoh: Bearer {token}'
)]
class OpenApi {}
