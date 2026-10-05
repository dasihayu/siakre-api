<?php

namespace App\Helpers;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ApiResponse
{
    /**
     * Format respons autentikasi (Login Berhasil - 200 OK)
     */
    public static function auth(mixed $user, string $token, string $message = 'Login berhasil', int $code = 200): JsonResponse
    {
        $userArray = [
            'id' => $user->id ?? null,
            'nama' => $user->nama ?? $user->name ?? null,
            'email' => $user->email ?? null,
            'role' => isset($user->role) && is_object($user->role) && method_exists($user->role, 'value')
                ? $user->role->value
                : ($user->role ?? null),
        ];

        return new JsonResponse([
            'success' => true,
            'code' => $code,
            'message' => $message,
            'data' => [
                'user' => $userArray,
                'authorization' => [
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                    'expires_in' => (int) config('jwt.ttl', 60) * 60,
                ],
            ],
        ], $code);
    }

    /**
     * Format respons sukses data tunggal atau daftar (200 OK / 201 Created)
     */
    public static function success(mixed $data = null, string $message = 'Berhasil', int $code = 200): JsonResponse
    {
        if ($data instanceof LengthAwarePaginator) {
            return self::paginated($data, $message, $code);
        }

        if ($data instanceof ResourceCollection && $data->resource instanceof LengthAwarePaginator) {
            return self::paginatedCollection($data, $message, $code);
        }

        return new JsonResponse([
            'success' => true,
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * Format respons sukses dengan paginasi (200 OK)
     */
    public static function paginated(LengthAwarePaginator $paginator, string $message = 'Berhasil', int $code = 200): JsonResponse
    {
        return new JsonResponse([
            'success' => true,
            'code' => $code,
            'message' => $message,
            'data' => $paginator->items(),
            'pagination' => [
                'total' => $paginator->total(),
                'per_page' => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'next_page_url' => $paginator->nextPageUrl(),
                'prev_page_url' => $paginator->previousPageUrl(),
            ],
        ], $code);
    }

    /**
     * Format respons sukses koleksi dengan paginasi (200 OK)
     */
    public static function paginatedCollection(ResourceCollection $collection, string $message = 'Berhasil', int $code = 200): JsonResponse
    {
        /** @var LengthAwarePaginator $paginator */
        $paginator = $collection->resource;

        return new JsonResponse([
            'success' => true,
            'code' => $code,
            'message' => $message,
            'data' => $collection->resolve(),
            'pagination' => [
                'total' => $paginator->total(),
                'per_page' => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'next_page_url' => $paginator->nextPageUrl(),
                'prev_page_url' => $paginator->previousPageUrl(),
            ],
        ], $code);
    }

    /**
     * Format respons error validasi (422 Unprocessable Entity)
     */
    public static function validationError(mixed $errors, string $message = 'Validasi gagal', int $code = 422): JsonResponse
    {
        return new JsonResponse([
            'success' => false,
            'code' => $code,
            'message' => $message,
            'errors' => $errors,
            'data' => null,
        ], $code);
    }

    /**
     * Format respons error umum (401 / 403 / 404 / 500)
     */
    public static function error(string $message = 'Terjadi kesalahan', int $code = 400, mixed $errors = null): JsonResponse
    {
        return new JsonResponse([
            'success' => false,
            'code' => $code,
            'message' => $message,
            'errors' => $errors,
            'data' => null,
        ], $code);
    }
}
