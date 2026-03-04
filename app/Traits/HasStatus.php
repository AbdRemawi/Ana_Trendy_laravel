<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * Has Status Trait
 *
 * Standardizes status field handling across models.
 * Provides common status constants and scopes.
 */
trait HasStatus
{
    /**
     * Status constants.
     */
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    /**
     * Boot the trait.
     * Ensures model has status attribute.
     */
    protected static function bootHasStatus()
    {
        // Auto-cast status to lowercase
        static::saving(function ($model) {
            if (isset($model->attributes['status'])) {
                $model->attributes['status'] = strtolower($model->attributes['status']);
            }
        });
    }

    /**
     * Scope to filter by active status.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope to filter by inactive status.
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_INACTIVE);
    }

    /**
     * Check if model is active.
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Check if model is inactive.
     */
    public function isInactive(): bool
    {
        return $this->status === self::STATUS_INACTIVE;
    }

    /**
     * Set status to active.
     */
    public function markAsActive(): void
    {
        $this->update(['status' => self::STATUS_ACTIVE]);
    }

    /**
     * Set status to inactive.
     */
    public function markAsInactive(): void
    {
        $this->update(['status' => self::STATUS_INACTIVE]);
    }

    /**
     * Toggle status between active and inactive.
     */
    public function toggleStatus(): void
    {
        $this->update([
            'status' => $this->isActive() ? self::STATUS_INACTIVE : self::STATUS_ACTIVE
        ]);
    }

    /**
     * Get available status options for forms.
     */
    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_ACTIVE => __('admin.status_active'),
            self::STATUS_INACTIVE => __('admin.status_inactive'),
        ];
    }
}
