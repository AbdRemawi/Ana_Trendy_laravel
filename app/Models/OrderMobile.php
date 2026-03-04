<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderMobile extends Model
{
    /**
     * Disable timestamps (table has no created_at/updated_at).
     */
    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $fillable = [
        'order_id',
        'phone_number',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function getFormattedPhoneNumberAttribute(): string
    {
        return $this->phone_number;
    }
}
