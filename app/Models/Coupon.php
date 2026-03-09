<?php

namespace App\Models;

use App\Enums\CouponType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'type',
        'value',
        'minimum_order_amount',
        'max_uses',
        'used_count',
        'valid_from',
        'valid_until',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'minimum_order_amount' => 'decimal:2',
            'valid_from' => 'datetime',
            'valid_until' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeValid($query)
    {
        return $query->where('valid_from', '<=', now())
            ->where(function ($q) {
                $q->whereNull('valid_until')
                    ->orWhere('valid_until', '>=', now());
            });
    }

    public function scopeAvailable($query)
    {
        return $query->active()
            ->valid()
            ->where(function ($q) {
                $q->whereNull('max_uses')
                    ->orWhereColumn('used_count', '<', 'max_uses');
            });
    }

    public function hasRemainingUses(): bool
    {
        if (is_null($this->max_uses)) {
            return true;
        }

        return $this->used_count < $this->max_uses;
    }

    public function isValidNow(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if (now()->lt($this->valid_from)) {
            return false;
        }

        if (!is_null($this->valid_until) && now()->gt($this->valid_until)) {
            return false;
        }

        return true;
    }

    public function isValidForOrderAmount(float $orderAmount): bool
    {
        return $orderAmount >= $this->minimum_order_amount;
    }

    public function incrementUsedCount(): void
    {
        $this->increment('used_count');
    }

    public function decrementUsedCount(): void
    {
        $this->decrement('used_count');
    }

    public function getTypeLabelAttribute(): string
    {
        return CouponType::from($this->type)->label();
    }

    public function getRemainingUsesAttribute(): ?int
    {
        if (is_null($this->max_uses)) {
            return null;
        }

        return max(0, $this->max_uses - $this->used_count);
    }

    public function isExpired(): bool
    {
        if (is_null($this->valid_until)) {
            return false;
        }

        return now()->gt($this->valid_until);
    }

    public function isNotYetStarted(): bool
    {
        if (is_null($this->valid_from)) {
            return false;
        }
        return now()->lt($this->valid_from);
    }
}
