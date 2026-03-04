<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * User status constants
     */
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_SUSPENDED = 'suspended';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'mobile',
        'email',
        'password',
        'status',
        'coupon_code',
        'commission_rate',
        'total_earnings',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    /**
     * Check if user has super admin role.
     * Uses Spatie's hasRole() method for role checking.
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }

    /**
     * Check if user is any type of admin (super_admin or admin).
     * Uses Spatie's hasAnyRole() method.
     */
    public function isAdmin(): bool
    {
        return $this->hasAnyRole(['super_admin', 'admin']);
    }

    /**
     * Check if user has affiliate role.
     * Uses Spatie's hasRole() method.
     */
    public function isAffiliate(): bool
    {
        return $this->hasRole('affiliate');
    }

    /**
     * Check if user account is active.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Get user's affiliate referral coupon code.
     *
     * @return string|null
     */
    public function getReferralCoupon(): ?string
    {
        return $this->coupon_code;
    }

    /**
     * Get user's commission rate.
     *
     * @return float
     */
    public function getCommissionRate(): float
    {
        return (float) $this->commission_rate;
    }

    /**
     * Get user's total earnings.
     *
     * @return float
     */
    public function getTotalEarnings(): float
    {
        return (float) $this->total_earnings;
    }
}
