<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\Role\StoreRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of roles.
     */
    public function index(): JsonResponse
    {
        $roles = Role::with('permissions')->latest()->paginate(10);

        return ApiResponse::success(
            RoleResource::collection($roles),
            'Role list retrieved successfully'
        );
    }

    /**
     * Store a newly created role in storage.
     */
    public function store(StoreRoleRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $role = Role::create([
            'name' => $validated['name'],
            'guard_name' => $validated['guard_name'] ?? 'api',
        ]);

        if (isset($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        $role->load('permissions');

        return ApiResponse::success(
            new RoleResource($role),
            'Role created successfully',
            201
        );
    }

    /**
     * Display the specified role.
     */
    public function show(Role $role): JsonResponse
    {
        $role->load('permissions');

        return ApiResponse::success(
            new RoleResource($role),
            'Role detail retrieved successfully'
        );
    }

    /**
     * Update the specified role in storage.
     */
    public function update(UpdateRoleRequest $request, Role $role): JsonResponse
    {
        $validated = $request->validated();

        if (isset($validated['name'])) {
            $role->name = $validated['name'];
        }

        if (isset($validated['guard_name'])) {
            $role->guard_name = $validated['guard_name'];
        }

        $role->save();

        if (isset($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        $role->load('permissions');

        return ApiResponse::success(
            new RoleResource($role),
            'Role updated successfully'
        );
    }

    /**
     * Remove the specified role from storage.
     */
    public function destroy(Role $role): JsonResponse
    {
        $role->delete();

        return ApiResponse::success(
            null,
            'Role deleted successfully'
        );
    }
}
