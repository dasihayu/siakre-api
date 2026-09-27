<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\Permission\StorePermissionRequest;
use App\Http\Requests\Permission\UpdatePermissionRequest;
use App\Http\Resources\PermissionResource;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Display a listing of permissions.
     */
    public function index(): JsonResponse
    {
        $permissions = Permission::latest()->paginate(15);

        return ApiResponse::success(
            PermissionResource::collection($permissions),
            'Permission list retrieved successfully'
        );
    }

    /**
     * Store a newly created permission in storage.
     */
    public function store(StorePermissionRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $permission = Permission::create([
            'name' => $validated['name'],
            'guard_name' => $validated['guard_name'] ?? 'api',
        ]);

        return ApiResponse::success(
            new PermissionResource($permission),
            'Permission created successfully',
            201
        );
    }

    /**
     * Display the specified permission.
     */
    public function show(Permission $permission): JsonResponse
    {
        return ApiResponse::success(
            new PermissionResource($permission),
            'Permission detail retrieved successfully'
        );
    }

    /**
     * Update the specified permission in storage.
     */
    public function update(UpdatePermissionRequest $request, Permission $permission): JsonResponse
    {
        $validated = $request->validated();

        if (isset($validated['name'])) {
            $permission->name = $validated['name'];
        }

        if (isset($validated['guard_name'])) {
            $permission->guard_name = $validated['guard_name'];
        }

        $permission->save();

        return ApiResponse::success(
            new PermissionResource($permission),
            'Permission updated successfully'
        );
    }

    /**
     * Remove the specified permission from storage.
     */
    public function destroy(Permission $permission): JsonResponse
    {
        $permission->delete();

        return ApiResponse::success(
            null,
            'Permission deleted successfully'
        );
    }
}
