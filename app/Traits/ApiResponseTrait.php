<?php

namespace App\Traits;

use App\Helpers\ApiResponse;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;

trait ApiResponseTrait
{
    /**
     * Return auth response format (Login Successful - 200 OK)
     */
    protected function authResponse(mixed $user, string $token, ?array $permissions = null, string $message = 'Login successful', int $code = 200): JsonResponse
    {
        return ApiResponse::auth($user, $token, $permissions, $message, $code);
    }

    /**
     * Return single data or list success response format (200 OK / 201 Created)
     */
    protected function successResponse(mixed $data = null, string $message = 'Success', int $code = 200): JsonResponse
    {
        return ApiResponse::success($data, $message, $code);
    }

    /**
     * Return paginated success response format (200 OK)
     */
    protected function paginatedResponse(LengthAwarePaginator $paginator, string $message = 'Success', int $code = 200): JsonResponse
    {
        return ApiResponse::paginated($paginator, $message, $code);
    }

    /**
     * Return paginated collection success response format (200 OK)
     */
    protected function paginatedCollectionResponse(ResourceCollection $collection, string $message = 'Success', int $code = 200): JsonResponse
    {
        return ApiResponse::paginatedCollection($collection, $message, $code);
    }

    /**
     * Return validation error response format (422 Unprocessable Entity)
     */
    protected function validationErrorResponse(mixed $errors, string $message = 'Validation failed', int $code = 422): JsonResponse
    {
        return ApiResponse::validationError($errors, $message, $code);
    }

    /**
     * Return general error response format (401 / 403 / 404 / 500)
     */
    protected function errorResponse(string $message = 'An error occurred', int $code = 400, mixed $errors = null): JsonResponse
    {
        return ApiResponse::error($message, $code, $errors);
    }
}
