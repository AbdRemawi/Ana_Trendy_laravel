<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

/**
 * User Management Controller (Admin)
 *
 * Handles user CRUD operations in the admin dashboard.
 * All methods are protected by permission middleware via routes.
 * Uses Form Request validation classes for clean separation of concerns.
 *
 * Security Features:
 * - Self-deletion prevention
 * - Self-role modification prevention
 * - Super admin role assignment protection
 * - Spatie handles role assignment atomically
 */
class UserController extends Controller
{
    /**
     * Display a listing of users.
     * Authorization is handled via route middleware.
     */
    public function index(Request $request): View
    {
        $query = User::query()->with('roles');

        // Filter by role if requested
        if ($request->filled('role')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        // Search by name, email, or mobile
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(20);
        $roles = Role::all()->pluck('name', 'name');

        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Display the specified user.
     * Authorization is handled via route middleware.
     */
    public function show(User $user): View
    {
        $user->load(['roles', 'permissions']);

        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for creating a new user.
     * Authorization is handled via route middleware.
     */
    public function create(): View
    {
        $roles = Role::all()->pluck('name', 'name');

        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created user.
     * Authorization is handled via route middleware.
     *
     * SECURITY:
     * - Prevents super_admin role assignment unless current user is super_admin
     * - Password is automatically hashed via model cast
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // SECURITY: Prevent non-super_admin from assigning super_admin role
        if ($validated['role'] === 'super_admin' && !auth()->user()->isSuperAdmin()) {
            return back()
                ->with('error', __('admin.cannot_assign_super_admin'))
                ->withInput();
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'] ?? null,
            'mobile' => $validated['mobile'],
            'password' => $validated['password'], // Hashed via model cast
            'status' => $validated['status'],
        ]);

        // Assign single role using sync (syncRoles handles transactions internally)
        $user->syncRoles([$validated['role']]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', __('admin.user_created_successfully'));
    }

    /**
     * Show the form for editing the specified user.
     * Authorization is handled via route middleware.
     */
    public function edit(User $user): View
    {
        $user->load('roles');
        $roles = Role::all()->pluck('name', 'name');

        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user.
     * Authorization is handled via route middleware.
     *
     * SECURITY:
     * - Prevents self-role modification
     * - Prevents super_admin role assignment unless current user is super_admin
     * - Password update is optional (nullable in validation)
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $validated = $request->validated();
        $currentUser = auth()->id();

        // SECURITY: Prevent modifying own role
        if ($user->id === $currentUser && isset($validated['role'])) {
            $currentRole = $user->roles->first()?->name;
            if ($currentRole !== $validated['role']) {
                return back()
                    ->with('error', __('admin.cannot_modify_own_role'))
                    ->withInput();
            }
        }

        // SECURITY: Prevent non-super_admin from assigning super_admin role
        if ($validated['role'] === 'super_admin' && !auth()->user()->isSuperAdmin()) {
            return back()
                ->with('error', __('admin.cannot_assign_super_admin'))
                ->withInput();
        }

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'] ?? null,
            'mobile' => $validated['mobile'],
            'status' => $validated['status'],
        ];

        // Only update password if provided
        if (!empty($validated['password'])) {
            $updateData['password'] = $validated['password'];
        }

        $user->update($updateData);

        // Sync role (syncRoles handles transactions internally)
        $user->syncRoles([$validated['role']]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', __('admin.user_updated_successfully'));
    }

    /**
     * Remove the specified user.
     * Authorization is handled via route middleware.
     *
     * SECURITY:
     * - Cannot delete super_admin users
     * - Cannot delete own account
     */
    public function destroy(User $user): RedirectResponse
    {
        // SECURITY: Prevent deletion of super admin users
        if ($user->hasRole('super_admin')) {
            return back()
                ->with('error', __('admin.cannot_delete_super_admin_user'));
        }

        // SECURITY: Prevent self-deletion
        if ($user->id === auth()->id()) {
            return back()
                ->with('error', __('admin.cannot_delete_own_account'));
        }

        $userName = $user->name;
        $user->delete();

        // Clear Spatie permission cache
        app()['cache']->forget('spatie.permission.cache');

        return redirect()
            ->route('admin.users.index')
            ->with('success', __('admin.user_deleted_successfully', ['name' => $userName]));
    }
}
