<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Helpers\ApiResponse;
use App\Http\Requests\Auth\GoogleLoginRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    /**
     * Get a JWT via given credentials.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        if (! $token = Auth::guard('api')->attempt($credentials)) {
            return ApiResponse::error('Email atau password salah.', 401);
        }

        /** @var User $user */
        $user = Auth::guard('api')->user();

        return ApiResponse::auth($user, $token, message: 'Login berhasil');
    }

    /**
     * Login or register via Google authentication.
     */
    public function googleLogin(GoogleLoginRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = User::where('google_id', $data['google_id'])
            ->orWhere('email', $data['email'])
            ->first();

        if ($user) {
            $updates = [];
            if (empty($user->google_id)) {
                $updates['google_id'] = $data['google_id'];
            }
            if (isset($data['avatar']) && $user->avatar !== $data['avatar']) {
                $updates['avatar'] = $data['avatar'];
            }
            if (! empty($updates)) {
                $user->update($updates);
            }
        } else {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'google_id' => $data['google_id'],
                'avatar' => $data['avatar'] ?? null,
                'role' => UserRole::DOSEN,
                'email_verified_at' => now(),
            ]);

            Role::findOrCreate(UserRole::DOSEN->value, 'api');
            $user->assignRole(UserRole::DOSEN->value);
        }

        /** @var string $token */
        $token = Auth::guard('api')->login($user);

        return ApiResponse::auth($user, $token, $user->permissions_array, message: 'Login Google berhasil');
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
        ], 'Profil pengguna berhasil diambil');
    }

    /**
     * Log the user out (Invalidate the token).
     */
    public function logout(): JsonResponse
    {
        Auth::guard('api')->logout();

        return ApiResponse::success(null, 'Berhasil logout');
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

        return ApiResponse::auth($user, $token, message: 'Token berhasil diperbarui');
    }
}
