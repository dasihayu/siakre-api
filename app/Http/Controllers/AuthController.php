<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Get a JWT via given credentials.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        if (! $token = Auth::guard('api')->attempt($credentials)) {
            return ApiResponse::error('Invalid email or password.', 401);
        }

        /** @var User $user */
        $user = Auth::guard('api')->user();

        return ApiResponse::auth($user, $token, message: 'Login successful');
    }

    /**
     * Get the authenticated User.
     */
    public function me(): JsonResponse
    {
        /** @var User $user */
        $user = Auth::guard('api')->user();

        return ApiResponse::success([
            'user' => [
                'id' => $user->id,
                'nama' => $user->nama ?? $user->name,
                'email' => $user->email,
                'role' => is_object($user->role) ? $user->role->value : $user->role,
            ],
        ], 'User profile retrieved successfully');
    }

    /**
     * Log the user out (Invalidate the token).
     */
    public function logout(): JsonResponse
    {
        Auth::guard('api')->logout();

        return ApiResponse::success(null, 'Successfully logged out');
    }

    /**
     * Refresh a token.
     */
    public function refresh(): JsonResponse
    {
        /** @var string $token */
        $token = Auth::guard('api')->refresh();

        /** @var User $user */
        $user = Auth::guard('api')->user();

        return ApiResponse::auth($user, $token, message: 'Token refreshed successfully');
    }
}
