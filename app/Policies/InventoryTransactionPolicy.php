<?php

namespace App\Policies;

use App\Models\InventoryTransaction;
use App\Models\User;

/**
 * Inventory Transaction Policy
 *
 * Authorization rules for InventoryTransaction model.
 * All actions require 'view products' or 'manage products' permission.
 */
class InventoryTransactionPolicy
{
    /**
     * Determine whether the user can view any inventory transactions.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view products') || $user->can('manage products');
    }

    /**
     * Determine whether the user can view a specific transaction.
     */
    public function view(User $user, InventoryTransaction $transaction): bool
    {
        return $this->viewAny($user);
    }

    /**
     * Determine whether the user can create transactions.
     */
    public function create(User $user): bool
    {
        return $user->can('manage products');
    }

    /**
     * Determine whether the user can update a transaction.
     */
    public function update(User $user, InventoryTransaction $transaction): bool
    {
        return $user->can('manage products');
    }

    /**
     * Determine whether the user can delete a transaction.
     */
    public function delete(User $user, InventoryTransaction $transaction): bool
    {
        return $user->can('manage products');
    }
}
