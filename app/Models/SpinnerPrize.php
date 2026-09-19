<?php

namespace App\Models;

use App\Enums\SpinnerPrizeType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SpinnerPrize extends Model
{
    protected $fillable = [
        'label',
        'type',
        'coupon_value',
        'coupon_min_order',
        'coupon_validity_days',
        'weight',
        'color',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'coupon_value' => 'decimal:2',
            'coupon_min_order' => 'decimal:2',
            'coupon_validity_days' => 'integer',
            'weight' => 'integer',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function spins(): HasMany
    {
        return $this->hasMany(SpinnerSpin::class);
    }

    /**
     * Active segments in display order (everything shown on the wheel, incl. decoys).
     */
    public function scopeDisplayable($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    /**
     * Segments that can actually be won (active AND weight > 0).
     */
    public function scopeWinnable($query)
    {
        return $query->where('is_active', true)->where('weight', '>', 0);
    }

    public function typeEnum(): SpinnerPrizeType
    {
        return SpinnerPrizeType::from($this->type);
    }

    public function isWinning(): bool
    {
        return $this->typeEnum()->isWinning();
    }

    public function allowsRespin(): bool
    {
        return $this->typeEnum()->allowsRespin();
    }
}
