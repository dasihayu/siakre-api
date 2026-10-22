<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Services\TridharmaService;
use Illuminate\Http\Request;

class DosenController extends Controller
{
    public function __construct(
        protected TridharmaService $tridharmaService
    ) {}

    /**
     * Mengambil daftar dosen dari API Tridharma.
     */
    public function index(Request $request)
    {
        $token = $request->bearerToken();
        $params = $request->only(['search', 'status', 'per_page', 'page']);

        $result = $this->tridharmaService->getDosen($token, $params);

        if (! empty($result['success']) && isset($result['data'])) {
            return response()->json($result, 200);
        }

        if (isset($result['message']) && ! empty($result['code']) && $result['code'] == 401) {
            return ApiResponse::error($result['message'], 401);
        }

        return ApiResponse::success($result['data'] ?? $result, 'Daftar dosen berhasil diambil');
    }
}
