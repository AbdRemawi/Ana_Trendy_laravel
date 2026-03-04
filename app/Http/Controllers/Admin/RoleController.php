<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

/**
 * Role Management Controller.
 *
 * Handles CRUD operations for roles including permission assignments.
 * All actions require 'manage roles' permission via policy and middleware.
 *
 * @author  Generated with Claude Code
 */
class RoleController extends Controller
{
    /**
     * Display a listing of all roles.
     *
     * PERFORMANCE: Paginated to prevent loading all roles at once.
     * Eager loads permission counts to prevent N+1 queries.
     *
     * @return View
     */
    public function index(): View
    {
        Gate::authorize('viewAny', Role::class);

        // Paginate results (25 per page) for better performance
        $roles = Role::withCount('permissions')
            ->orderBy('name')
            ->paginate(25);

        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new role.
     *
     * @return View
     */
    public function create(): View
    {
        Gate::authorize('create', Role::class);

        // Load all available permissions for the form
        $allPermissions = \Spatie\Permission\Models\Permission::all()
            ->pluck('name')
            ->toArray();

        return view('admin.roles.create', compact('allPermissions'));
    }

    /**
     * Store a newly created role in storage.
     *
     * @param StoreRoleRequest $request
     * @return RedirectResponse
     */
    public function store(StoreRoleRequest $request): RedirectResponse
    {
        Gate::authorize('create', Role::class);

        $role = Role::create(['name' => $request->validated('name')]);

        // SECURITY: Automatically grant 'view dashboard' permission to all new roles
        // This is a required permission for all admin roles to access the dashboard
        $dashboardPermission = 'view dashboard';
        $role->givePermissionTo($dashboardPermission);

        // Clear Spatie permission cache
        app()['cache']->forget('spatie.permission.cache');

        return redirect()
            ->route('admin.roles.index')
            ->with('success', __('admin.role_created', ['name' => $role->name]));
    }

    /**
     * Display the specified role with its permissions.
     *
     * @param Role $role
     * @return View
     */
    public function show(Role $role): View
    {
        Gate::authorize('view', $role);

        $role->load('permissions');

        return view('admin.roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified role.
     *
     * @param Role $role
     * @return View
     */
    public function edit(Role $role): View
    {
        Gate::authorize('update', $role);

        $role->load('permissions');

        // Load all available permissions for the form
        $allPermissions = \Spatie\Permission\Models\Permission::all()
            ->pluck('name')
            ->toArray();

        // Get role's current permissions
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('admin.roles.edit', compact('role', 'allPermissions', 'rolePermissions'));
    }

    /**
     * Update the specified role in storage.
     *
     * @param UpdateRoleRequest $request
     * @param Role $role
     * @return RedirectResponse
     */
    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        Gate::authorize('update', $role);

        $validated = $request->validated();

        DB::transaction(function () use ($role, $validated) {
            // Update role name if provided
            if (isset($validated['name'])) {
                $role->update(['name' => $validated['name']]);
            }

            // Sync permissions if provided
            if (isset($validated['permissions'])) {
                $permissions = $validated['permissions'];

                // SECURITY: Always ensure 'view dashboard' permission is included
                // This is a required permission for all admin roles to access the dashboard
                $dashboardPermission = 'view dashboard';
                if (!in_array($dashboardPermission, $permissions)) {
                    $permissions[] = $dashboardPermission;
                }

                $role->syncPermissions($permissions);
            }
        });

        // Clear Spatie permission cache
        app()['cache']->forget('spatie.permission.cache');

        return redirect()
            ->route('admin.roles.index')
            ->with('success', __('admin.role_updated'));
    }

    /**
     * Remove the specified role from storage.
     *
     * SECURITY PRECAUTIONS:
     * - Prevents deletion of the super_admin role (critical system role)
     * - Automatically removes role from all users before deletion (data integrity)
     * - Uses database transaction to ensure atomic operation
     * - Clears Spatie permission cache after deletion
     *
     * @param Role $role
     * @return RedirectResponse
     */
    public function destroy(Role $role): RedirectResponse
    {
        Gate::authorize('delete', $role);

        // SECURITY: Prevent deletion of super_admin role
        // This is a critical system role that must always exist
        if ($role->name === 'super_admin') {
            return back()
                ->with('error', __('admin.cannot_delete_super_admin'));
        }

        $roleName = $role->name;
        $userCount = $role->users()->count();

        DB::transaction(function () use ($role, $userCount) {
            // SECURITY: Remove role from all users before deleting
            // This prevents orphaned relationships and maintains data integrity
            // Spatie's syncRoles([]) will remove all roles, so we need a different approach
            if ($userCount > 0) {
                foreach ($role->users as $user) {
                    // Remove only this specific role from the user
                    $user->removeRole($role);
                }
            }

            // Now safe to delete the role
            $role->delete();
        });

        // Clear Spatie permission cache to ensure permissions are recalculated
        // This is critical - users who had this role will now have updated permissions
        app()['cache']->forget('spatie.permission.cache');

        $message = $userCount > 0
            ? __('admin.role_deleted_with_users', ['name' => $roleName, 'count' => $userCount])
            : __('admin.role_deleted', ['name' => $roleName]);

        return redirect()
            ->route('admin.roles.index')
            ->with('success', $message);
    }

    /**
     * API Endpoint: Get role with permissions for AJAX requests.
     *
     * @param Role $role
     * @return JsonResponse
     */
    public function get(Role $role): JsonResponse
    {
        Gate::authorize('view', $role);

        $role->load('permissions');

        return response()->json([
            'id' => $role->id,
            'name' => $role->name,
            'permissions' => $role->permissions->pluck('name'),
        ]);
    }
}
