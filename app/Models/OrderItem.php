<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    /**
     * Disable timestamps (table has no created_at/updated_at).
     */
    const CREATED_AT = null;
    const UPDATED_AT = null;

    /**
     * Disable timestamps completely.
     */
    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'base_price',
        'coupon_discount_per_unit',
        'unit_sale_price',
        'unit_cost_price',
        'total_price',
    ];

    protected function casts(): array
    {
        return [
            'base_price' => 'decimal:2',
            'coupon_discount_per_unit' => 'decimal:2',
            'unit_sale_price' => 'decimal:2',
            'unit_cost_price' => 'decimal:2',
            'total_price' => 'decimal:2',
        ];
    }

    protected $hidden = [
        'updated_at',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getProfitAttribute(): float
    {
        return (float) (($this->unit_sale_price - $this->unit_cost_price) * $this->quantity);
    }

    public function getProfitMarginAttribute(): float
    {
        if ($this->unit_cost_price <= 0) {
            return 0;
        }

        $profit = $this->unit_sale_price - $this->unit_cost_price;
        return round(($profit / $this->unit_cost_price) * 100, 2);
    }

    public function getTotalDiscountAttribute(): float
    {
        return (float) ($this->coupon_discount_per_unit * $this->quantity);
    }

    public function getOriginalPriceAttribute(): float
    {
        return (float) ($this->base_price * $this->quantity);
    }
}
