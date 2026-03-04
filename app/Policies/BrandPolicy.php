<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Brand;

/**
 * Policy for Brand authorization.
 *
 * All brand management actions require specific permissions.
 * This policy is used in conjunction with middleware to ensure
 * only authorized users can manage brands.
 *
 * Permission structure:
 * - view brands: Can view the brands list and individual brand details
 * - manage brands: Can create and update brands
 * - delete brands: Can delete (soft delete) brands
 */
class BrandPolicy
{
    /**
     * Determine whether the user can view any brands.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view brands');
    }

    /**
     * Determine whether the user can view a specific brand.
     */
    public function view(User $user, Brand $brand): bool
    {
        return $user->can('view brands');
    }

    /**
     * Determine whether the user can create brands.
     */
    public function create(User $user): bool
    {
        return $user->can('manage brands');
    }

    /**
     * Determine whether the user can update a brand.
     */
    public function update(User $user, Brand $brand): bool
    {
        return $user->can('manage brands');
    }

    /**
     * Determine whether the user can delete a brand.
     */
    public function delete(User $user, Brand $brand): bool
    {
        return $user->can('delete brands');
    }

    /**
     * Determine whether the user can restore a brand.
     */
    public function restore(User $user, Brand $brand): bool
    {
        return $user->can('delete brands');
    }

    /**
     * Determine whether the user can force delete a brand.
     */
    public function forceDelete(User $user, Brand $brand): bool
    {
        return $user->can('delete brands');
    }
}
