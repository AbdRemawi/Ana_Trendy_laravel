<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Spatie\Permission\Models\Permission;

/**
 * Policy for Permission authorization.
 *
 * All permission management actions require 'manage permissions' permission.
 * This policy is used in conjunction with middleware to ensure
 * only authorized users can manage permissions.
 */
class PermissionPolicy
{
    /**
     * Determine whether the user can view any permissions.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('manage permissions');
    }

    /**
     * Determine whether the user can view a specific permission.
     */
    public function view(User $user, Permission $permission): bool
    {
        return $user->can('manage permissions');
    }

    /**
     * Determine whether the user can create permissions.
     */
    public function create(User $user): bool
    {
        return $user->can('manage permissions');
    }

    /**
     * Determine whether the user can update a permission.
     */
    public function update(User $user, Permission $permission): bool
    {
        return $user->can('manage permissions');
    }

    /**
     * Determine whether the user can delete a permission.
     *
     * Additional validation is performed in the controller to prevent
     * deletion of permissions that are assigned to roles. This policy
     * only checks the base permission.
     */
    public function delete(User $user, Permission $permission): bool
    {
        return $user->can('manage permissions');
    }

    /**
     * Determine whether the user can restore a permission.
     * (Not implemented in current soft-delete setup)
     */
    public function restore(User $user, Permission $permission): bool
    {
        return $user->can('manage permissions');
    }

    /**
     * Determine whether the user can force delete a permission.
     * (Not implemented in current soft-delete setup)
     */
    public function forceDelete(User $user, Permission $permission): bool
    {
        return $user->can('manage permissions');
    }
}
