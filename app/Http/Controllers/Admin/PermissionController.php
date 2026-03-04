<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;

/**
 * Permission Management Controller.
 *
 * Handles CRUD operations for permissions.
 * All actions require 'manage permissions' permission via policy and middleware.
 *
 * @author  Generated with Claude Code
 */
class PermissionController extends Controller
{
    /**
     * Display a listing of all permissions.
     *
     * PERFORMANCE: Paginated to prevent loading all permissions at once.
     * Eager loads roles and counts to prevent N+1 queries.
     *
     * @return View
     */
    public function index(): View
    {
        Gate::authorize('viewAny', Permission::class);

        // Paginate results (25 per page) for better performance
        $permissions = Permission::with('roles')
            ->withCount('roles')
            ->orderBy('name')
            ->paginate(25);

        return view('admin.permissions.index', compact('permissions'));
    }

    /**
     * Show the form for creating a new permission.
     *
     * @return View
     */
    public function create(): View
    {
        Gate::authorize('create', Permission::class);

        return view('admin.permissions.create');
    }

    /**
     * Store a newly created permission in storage.
     *
     * @param StorePermissionRequest $request
     * @return RedirectResponse
     */
    public function store(StorePermissionRequest $request): RedirectResponse
    {
        Gate::authorize('create', Permission::class);

        $permission = Permission::create([
            'name' => $request->validated('name'),
            'guard_name' => 'web',
        ]);

        return redirect()
            ->route('admin.permissions.index')
            ->with('success', __('admin.permission_created', ['name' => $permission->name]));
    }

    /**
     * Display the specified permission.
     *
     * @param Permission $permission
     * @return View
     */
    public function show(Permission $permission): View
    {
        Gate::authorize('view', $permission);

        $permission->load('roles');

        return view('admin.permissions.show', compact('permission'));
    }

    /**
     * Show the form for editing the specified permission.
     *
     * @param Permission $permission
     * @return View
     */
    public function edit(Permission $permission): View
    {
        Gate::authorize('update', $permission);

        return view('admin.permissions.edit', compact('permission'));
    }

    /**
     * Update the specified permission in storage.
     *
     * @param UpdatePermissionRequest $request
     * @param Permission $permission
     * @return RedirectResponse
     */
    public function update(UpdatePermissionRequest $request, Permission $permission): RedirectResponse
    {
        Gate::authorize('update', $permission);

        $permission->update([
            'name' => $request->validated('name'),
        ]);

        // Clear Spatie permission cache
        app()['cache']->forget('spatie.permission.cache');

        return redirect()
            ->route('admin.permissions.index')
            ->with('success', __('admin.permission_updated'));
    }

    /**
     * Remove the specified permission from storage.
     *
     * Prevents deletion if the permission is assigned to any roles.
     *
     * @param Permission $permission
     * @return RedirectResponse
     */
    public function destroy(Permission $permission): RedirectResponse
    {
        Gate::authorize('delete', $permission);

        // Check if permission is assigned to any roles
        if ($permission->roles()->count() > 0) {
            $roleNames = $permission->roles->pluck('name')->join(', ');
            return back()
                ->with('error', __('admin.cannot_delete_permission_in_use', ['roles' => $roleNames]));
        }

        $permissionName = $permission->name;
        $permission->delete();

        // Clear Spatie permission cache
        app()['cache']->forget('spatie.permission.cache');

        return redirect()
            ->route('admin.permissions.index')
            ->with('success', __('admin.permission_deleted', ['name' => $permissionName]));
    }
}
