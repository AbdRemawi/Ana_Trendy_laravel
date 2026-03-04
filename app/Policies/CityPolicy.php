<?php

namespace App\Policies;

use App\Models\City;
use App\Models\User;

/**
 * City Policy
 *
 * Authorization rules for City model.
 * All actions require 'view cities' or 'manage cities' permission.
 */
class CityPolicy
{
    /**
     * Determine whether the user can view any cities.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view cities') || $user->can('manage cities');
    }

    /**
     * Determine whether the user can view a specific city.
     */
    public function view(User $user, City $city): bool
    {
        return $this->viewAny($user);
    }

    /**
     * Determine whether the user can create cities.
     */
    public function create(User $user): bool
    {
        return $user->can('manage cities');
    }

    /**
     * Determine whether the user can update a city.
     */
    public function update(User $user, City $city): bool
    {
        return $user->can('manage cities');
    }

    /**
     * Determine whether the user can delete a city.
     */
    public function delete(User $user, City $city): bool
    {
        return $user->can('manage cities');
    }

    /**
     * Determine whether the user can restore a city.
     */
    public function restore(User $user, City $city): bool
    {
        return $user->can('manage cities');
    }

    /**
     * Determine whether the user can toggle status.
     */
    public function toggleStatus(User $user, City $city): bool
    {
        return $user->can('manage cities');
    }
}
