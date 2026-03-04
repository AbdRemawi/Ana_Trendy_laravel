<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

/**
 * Product Policy
 *
 * Authorization rules for Product model.
 * All actions require 'view products' or 'manage products' permission.
 */
class ProductPolicy
{
    /**
     * Determine whether the user can view any products.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view products') || $user->can('manage products');
    }

    /**
     * Determine whether the user can view a specific product.
     */
    public function view(User $user, Product $product): bool
    {
        return $this->viewAny($user);
    }

    /**
     * Determine whether the user can create products.
     */
    public function create(User $user): bool
    {
        return $user->can('manage products');
    }

    /**
     * Determine whether the user can update a product.
     */
    public function update(User $user, Product $product): bool
    {
        return $user->can('manage products');
    }

    /**
     * Determine whether the user can delete a product.
     */
    public function delete(User $user, Product $product): bool
    {
        return $user->can('delete products');
    }

    /**
     * Determine whether the user can restore a product.
     */
    public function restore(User $user, Product $product): bool
    {
        return $user->can('manage products');
    }

    /**
     * Determine whether the user can force delete a product.
     */
    public function forceDelete(User $user, Product $product): bool
    {
        return $user->can('manage products');
    }
}
