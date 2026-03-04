<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryTransaction extends Model
{
    use HasFactory;

    /**
     * Transaction type constants
     */
    public const TYPE_SUPPLY = 'supply';
    public const TYPE_SALE = 'sale';
    public const TYPE_RETURN = 'return';
    public const TYPE_DAMAGE = 'damage';
    public const TYPE_ADJUSTMENT = 'adjustment';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'product_id',
        'type',
        'quantity',
        'notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'product_id' => 'integer',
            'quantity' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the product that owns the transaction.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope to filter by transaction type.
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to get only positive stock changes (supply, return).
     */
    public function scopePositive($query)
    {
        return $query->whereIn('type', [self::TYPE_SUPPLY, self::TYPE_RETURN]);
    }

    /**
     * Scope to get only negative stock changes (sale, damage).
     */
    public function scopeNegative($query)
    {
        return $query->whereIn('type', [self::TYPE_SALE, self::TYPE_DAMAGE]);
    }

    /**
     * Get available transaction types as an array.
     */
    public static function getAvailableTypes(): array
    {
        return [
            self::TYPE_SUPPLY,
            self::TYPE_SALE,
            self::TYPE_RETURN,
            self::TYPE_DAMAGE,
            self::TYPE_ADJUSTMENT,
        ];
    }

    /**
     * Get transaction type label for display.
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_SUPPLY => 'Supply',
            self::TYPE_SALE => 'Sale',
            self::TYPE_RETURN => 'Return',
            self::TYPE_DAMAGE => 'Damage',
            self::TYPE_ADJUSTMENT => 'Adjustment',
            default => 'Unknown',
        };
    }

    /**
     * Check if transaction increases stock.
     */
    public function increasesStock(): bool
    {
        return in_array($this->type, [self::TYPE_SUPPLY, self::TYPE_RETURN])
            || ($this->type === self::TYPE_ADJUSTMENT && $this->quantity > 0);
    }

    /**
     * Check if transaction decreases stock.
     */
    public function decreasesStock(): bool
    {
        return in_array($this->type, [self::TYPE_SALE, self::TYPE_DAMAGE])
            || ($this->type === self::TYPE_ADJUSTMENT && $this->quantity < 0);
    }
}
