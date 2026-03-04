<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;

/**
 * Category Policy
 *
 * Authorization rules for Category model.
 * All actions require 'view categories' or 'manage categories' permission.
 */
class CategoryPolicy
{
    /**
     * Determine whether the user can view any categories.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view categories') || $user->can('manage categories');
    }

    /**
     * Determine whether the user can view a specific category.
     */
    public function view(User $user, Category $category): bool
    {
        return $this->viewAny($user);
    }

    /**
     * Determine whether the user can create categories.
     */
    public function create(User $user): bool
    {
        return $user->can('manage categories');
    }

    /**
     * Determine whether the user can update a category.
     */
    public function update(User $user, Category $category): bool
    {
        return $user->can('manage categories');
    }

    /**
     * Determine whether the user can delete a category.
     *
     * Additional validation for categories with children is handled in the controller.
     */
    public function delete(User $user, Category $category): bool
    {
        return $user->can('manage categories');
    }

    /**
     * Determine whether the user can restore a category.
     */
    public function restore(User $user, Category $category): bool
    {
        return $user->can('manage categories');
    }

    /**
     * Determine whether the user can force delete a category.
     */
    public function forceDelete(User $user, Category $category): bool
    {
        return $user->can('manage categories');
    }
}
