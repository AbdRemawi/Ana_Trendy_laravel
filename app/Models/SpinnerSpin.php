<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpinnerSpin extends Model
{
    protected $fillable = [
        'device_id',
        'token',
        'spinner_prize_id',
        'prize_label',
        'result_type',
        'phone_number',
        'coupon_id',
        'is_win',
        'can_respin',
        'is_redeemed',
        'ip_address',
        'played_on',
    ];

    protected function casts(): array
    {
        return [
            'is_win' => 'boolean',
            'can_respin' => 'boolean',
            'is_redeemed' => 'boolean',
            'played_on' => 'date',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function prize(): BelongsTo
    {
        return $this->belongsTo(SpinnerPrize::class, 'spinner_prize_id');
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function scopePlayedToday($query)
    {
        return $query->whereDate('played_on', today());
    }
}
