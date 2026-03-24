<?php

namespace App\Services;

use App\Models\User;

/**
 * Affiliate Service
 *
 * Handles affiliate-related business logic.
 * Extracted from UserController for better separation of concerns.
 */
class AffiliateService
{
    /**
     * Check if a user is an affiliate.
     *
     * @param User $user
     * @return bool
     */
    public function isAffiliate(User $user): bool
    {
        return $user->hasRole('affiliate');
    }
}
