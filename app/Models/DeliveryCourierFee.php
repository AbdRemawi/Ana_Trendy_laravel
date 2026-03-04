<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryCourierFee extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'delivery_courier_id',
        'city_id',
        'real_fee_amount',
        'display_fee_amount',
        'currency',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'real_fee_amount' => 'decimal:3',
        'display_fee_amount' => 'decimal:3',
        'is_active' => 'boolean',
    ];

    /**
     * Get the courier that owns this fee.
     */
    public function courier(): BelongsTo
    {
        return $this->belongsTo(DeliveryCourier::class, 'delivery_courier_id');
    }

    /**
     * Get the city that owns this fee.
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Scope to only include active fees.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get fees for a specific courier and city.
     */
    public function scopeForCourierAndCity($query, $courierId, $cityId)
    {
        return $query->where('delivery_courier_id', $courierId)
                    ->where('city_id', $cityId);
    }

    /**
     * Calculate profit margin percentage.
     */
    public function getProfitMarginAttribute(): float
    {
        if ($this->real_fee_amount == 0) {
            return 0;
        }

        return (($this->display_fee_amount - $this->real_fee_amount) / $this->real_fee_amount) * 100;
    }

    /**
     * Calculate profit amount.
     */
    public function getProfitAmountAttribute(): float
    {
        return $this->display_fee_amount - $this->real_fee_amount;
    }
}
