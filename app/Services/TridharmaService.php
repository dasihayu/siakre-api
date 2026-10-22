<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TridharmaService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.tridharma.url', 'https://tridharma.fayra.my.id'), '/');
    }

    /**
     * Mengambil daftar dosen dari API Tridharma.
     *
     * @param  array<string, mixed>  $queryParams
     * @return array<string, mixed>
     */
    public function getDosen(?string $token = null, array $queryParams = []): array
    {
        $request = Http::acceptJson();

        if ($token) {
            $request = $request->withToken($token);
        }

        $response = $request->get("{$this->baseUrl}/api/dosen", $queryParams);

        return $response->json() ?? [];
    }
}
