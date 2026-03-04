<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Spatie\Permission\Models\Role;

/**
 * Policy for Role authorization.
 *
 * All role management actions require 'manage roles' permission.
 * This policy is used in conjunction with middleware to ensure
 * only authorized users can manage roles.
 */
class RolePolicy
{
    /**
     * Determine whether the user can view any roles.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('manage roles');
    }

    /**
     * Determine whether the user can view a specific role.
     */
    public function view(User $user, Role $role): bool
    {
        return $user->can('manage roles');
    }

    /**
     * Determine whether the user can create roles.
     */
    public function create(User $user): bool
    {
        return $user->can('manage roles');
    }

    /**
     * Determine whether the user can update a role.
     */
    public function update(User $user, Role $role): bool
    {
        return $user->can('manage roles');
    }

    /**
     * Determine whether the user can delete a role.
     *
     * Additional validation is performed in the controller to prevent
     * deletion of the super_admin role. This policy only checks permission.
     */
    public function delete(User $user, Role $role): bool
    {
        return $user->can('manage roles');
    }

    /**
     * Determine whether the user can restore a role.
     * (Not implemented in current soft-delete setup)
     */
    public function restore(User $user, Role $role): bool
    {
        return $user->can('manage roles');
    }

    /**
     * Determine whether the user can force delete a role.
     * (Not implemented in current soft-delete setup)
     */
    public function forceDelete(User $user, Role $role): bool
    {
        return $user->can('manage roles');
    }
}
