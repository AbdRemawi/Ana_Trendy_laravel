<?php

namespace App\Policies;

use App\Models\User;

/**
 * Policy for User authorization.
 *
 * All user management actions require 'manage users' permission.
 * This policy is used in conjunction with middleware to ensure
 * only authorized users can manage users.
 *
 * Additional security:
 * - Users cannot delete themselves
 * - Super admin role assignment is protected
 */
class UserPolicy
{
    /**
     * Determine whether the user can view any users.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('manage users');
    }

    /**
     * Determine whether the user can view a specific user.
     */
    public function view(User $user, User $model): bool
    {
        return $user->can('manage users');
    }

    /**
     * Determine whether the user can create users.
     */
    public function create(User $user): bool
    {
        return $user->can('manage users');
    }

    /**
     * Determine whether the user can update a user.
     *
     * Users can update themselves if they have manage users permission,
     * but additional restrictions may apply in the controller.
     */
    public function update(User $user, User $model): bool
    {
        return $user->can('manage users');
    }

    /**
     * Determine whether the user can delete a user.
     *
     * Note: The controller adds additional validation to prevent:
     * - Self-deletion
     * - Deletion of super_admin users
     */
    public function delete(User $user, User $model): bool
    {
        return $user->can('manage users');
    }

    /**
     * Determine whether the user can restore a user.
     * (Not implemented in current soft-delete setup)
     */
    public function restore(User $user, User $model): bool
    {
        return $user->can('manage users');
    }

    /**
     * Determine whether the user can force delete a user.
     * (Not implemented in current soft-delete setup)
     */
    public function forceDelete(User $user, User $model): bool
    {
        return $user->can('manage users');
    }
}
