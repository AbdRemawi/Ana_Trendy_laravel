<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliveryCourier extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'contact_phone',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get all delivery fees for this courier.
     */
    public function deliveryFees(): HasMany
    {
        return $this->hasMany(DeliveryCourierFee::class, 'delivery_courier_id');
    }

    /**
     * Scope to only include active couriers.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
