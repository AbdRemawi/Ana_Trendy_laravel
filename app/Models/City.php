<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'is_active',
        'default_delivery_fee',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'default_delivery_fee' => 'decimal:3',
    ];

    /**
     * Get all delivery fees for this city.
     */
    public function deliveryFees(): HasMany
    {
        return $this->hasMany(DeliveryCourierFee::class, 'city_id');
    }

    /**
     * Scope to only include active cities.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
