<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'full_name',
        'city_id',
        'address',
        'delivery_courier_id',
        'real_delivery_fee',
        'subtotal_products',
        'coupon_id',
        'coupon_discount_amount',
        'free_delivery_discount',
        'actual_charge',
        'total_price_for_customer',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'real_delivery_fee' => 'decimal:3',
            'subtotal_products' => 'decimal:2',
            'coupon_discount_amount' => 'decimal:2',
            'free_delivery_discount' => 'decimal:3',
            'actual_charge' => 'decimal:2',
            'total_price_for_customer' => 'decimal:2',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    protected $hidden = [
        'updated_at',
    ];

    protected $appends = [
        'status_label',
    ];

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function deliveryCourier(): BelongsTo
    {
        return $this->belongsTo(DeliveryCourier::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function mobiles(): HasMany
    {
        return $this->hasMany(OrderMobile::class);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', OrderStatus::PROCESSING->value);
    }

    public function scopeWithDelivery($query)
    {
        return $query->where('status', OrderStatus::WITH_DELIVERY_COMPANY->value);
    }

    public function scopeReceived($query)
    {
        return $query->where('status', OrderStatus::RECEIVED->value);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', OrderStatus::CANCELLED->value);
    }

    public function scopeReturned($query)
    {
        return $query->where('status', OrderStatus::RETURNED->value);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', [
            OrderStatus::PROCESSING->value,
            OrderStatus::WITH_DELIVERY_COMPANY->value,
        ]);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', OrderStatus::RECEIVED->value);
    }

    public function scopeCancelledOrReturned($query)
    {
        return $query->whereIn('status', [
            OrderStatus::CANCELLED->value,
            OrderStatus::RETURNED->value,
        ]);
    }

    /**
     * Scope to filter orders by date range.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $filter Filter type: today, week, month, custom, or all
     * @param string|null $startDate Start date for custom filter
     * @param string|null $endDate End date for custom filter
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDateFilter($query, string $filter, ?string $startDate = null, ?string $endDate = null)
    {
        return match ($filter) {
            'today' => $query->whereDate('created_at', '=', \Carbon\Carbon::today()),
            'week' => $query->whereBetween('created_at', [
                \Carbon\Carbon::now()->startOfWeek(),
                \Carbon\Carbon::now()->endOfWeek()
            ]),
            'month' => $query->whereYear('created_at', '=', \Carbon\Carbon::now()->year)
                         ->whereMonth('created_at', '=', \Carbon\Carbon::now()->month),
            'custom' => $startDate && $endDate
                ? $query->whereBetween('created_at', [
                    \Carbon\Carbon::parse($startDate)->startOfDay(),
                    \Carbon\Carbon::parse($endDate)->endOfDay()
                ])
                : $query,
            default => $query, // All time - no filter
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return OrderStatus::from($this->status)->label();
    }

    public function isProcessing(): bool
    {
        return $this->status === OrderStatus::PROCESSING->value;
    }

    public function isWithDelivery(): bool
    {
        return $this->status === OrderStatus::WITH_DELIVERY_COMPANY->value;
    }

    public function isReceived(): bool
    {
        return $this->status === OrderStatus::RECEIVED->value;
    }

    public function isCancelled(): bool
    {
        return $this->status === OrderStatus::CANCELLED->value;
    }

    public function isReturned(): bool
    {
        return $this->status === OrderStatus::RETURNED->value;
    }

    public function hasCourier(): bool
    {
        return !is_null($this->delivery_courier_id);
    }

    public function hasCoupon(): bool
    {
        return !is_null($this->coupon_id);
    }

    public function getProfitAttribute(): float
    {
        $itemsRevenue = $this->items()->sum('total_price');
        // Calculate items cost by summing unit_cost_price * quantity
        $itemsCost = $this->items()->get()->sum(function ($item) {
            return $item->unit_cost_price * $item->quantity;
        });
        $deliveryCost = $this->real_delivery_fee ?? 0;

        return (float) ($itemsRevenue - $itemsCost - $deliveryCost);
    }

    public function getTotalItemsAttribute(): int
    {
        return (int) $this->items()->sum('quantity');
    }

    public function canAssignCourier(): bool
    {
        return $this->isProcessing() && !$this->hasCourier();
    }

    public function canTransitionTo(OrderStatus $status): bool
    {
        return OrderStatus::from($this->status)->canTransitionTo($status);
    }
}
