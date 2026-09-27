<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(): JsonResponse
    {
        $users = User::latest()->paginate(10);

        return ApiResponse::success(
            UserResource::collection($users),
            'User list retrieved successfully'
        );
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['nama'] ?? $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        $roleValue = is_object($user->role) ? $user->role->value : $user->role;
        $user->assignRole($roleValue);

        return ApiResponse::success(
            new UserResource($user),
            'User created successfully',
            201
        );
    }

    /**
     * Display the specified user.
     */
    public function show(User $user): JsonResponse
    {
        return ApiResponse::success(
            new UserResource($user),
            'User detail retrieved successfully'
        );
    }

    /**
     * Update the specified user in storage.
     */
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $validated = $request->validated();

        if (isset($validated['nama']) || isset($validated['name'])) {
            $user->name = $validated['nama'] ?? $validated['name'];
        }

        if (isset($validated['email'])) {
            $user->email = $validated['email'];
        }

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        if (isset($validated['role'])) {
            $user->role = $validated['role'];
            $roleValue = is_object($user->role) ? $user->role->value : $user->role;
            $user->syncRoles([$roleValue]);
        }

        $user->save();

        return ApiResponse::success(
            new UserResource($user),
            'User updated successfully'
        );
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return ApiResponse::success(
            null,
            'User deleted successfully'
        );
    }
}
