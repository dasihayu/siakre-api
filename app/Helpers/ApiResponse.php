<?php

namespace App\Helpers;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ApiResponse
{
    /**
     * Return auth response format (Login Successful - 200 OK)
     */
    public static function auth(mixed $user, string $token, ?array $permissions = null, string $message = 'Login successful', int $code = 200): JsonResponse
    {
        $userArray = [
            'id' => $user->id ?? null,
            'nama' => $user->nama ?? $user->name ?? null,
            'email' => $user->email ?? null,
            'role' => isset($user->role) && is_object($user->role) && method_exists($user->role, 'value')
                ? $user->role->value
                : ($user->role ?? null),
        ];

        $userPermissions = $permissions;
        if ($userPermissions === null) {
            if (is_object($user) && method_exists($user, 'getAllPermissions')) {
                $userPermissions = $user->getAllPermissions()->pluck('name')->toArray();
            }

            if (empty($userPermissions) && is_object($user) && isset($user->permissions_array)) {
                $userPermissions = $user->permissions_array;
            }

            $userPermissions = $userPermissions ?? [];
        }

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
                    'permissions' => $userPermissions,
                ],
            ],
        ], $code);
    }

    /**
     * Return single data or list success response format (200 OK / 201 Created)
     */
    public static function success(mixed $data = null, string $message = 'Success', int $code = 200): JsonResponse
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
     * Return paginated success response format (200 OK)
     */
    public static function paginated(LengthAwarePaginator $paginator, string $message = 'Success', int $code = 200): JsonResponse
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
     * Return paginated collection success response format (200 OK)
     */
    public static function paginatedCollection(ResourceCollection $collection, string $message = 'Success', int $code = 200): JsonResponse
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
     * Return validation error response format (422 Unprocessable Entity)
     */
    public static function validationError(mixed $errors, string $message = 'Validation failed', int $code = 422): JsonResponse
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
     * Return general error response format (401 / 403 / 404 / 500)
     */
    public static function error(string $message = 'An error occurred', int $code = 400, mixed $errors = null): JsonResponse
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
