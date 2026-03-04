<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Dashboard Policy
 *
 * Defines authorization rules for dashboard access.
 * All authenticated users with 'view dashboard' permission can access the dashboard.
 *
 * @package App\Policies
 */
class DashboardPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the dashboard.
     *
     * This policy enforces that users must have the 'view dashboard' permission
     * to access the dashboard, providing defense in depth alongside route middleware.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function view(User $user): bool
    {
        return $user->can('view dashboard');
    }

    /**
     * Determine whether the user can view dashboard statistics.
     *
     * Additional permission check for sensitive dashboard data.
     * Only admins and super_admins can see detailed statistics.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function viewStatistics(User $user): bool
    {
        return $user->isAdmin() && $user->isActive();
    }
}
