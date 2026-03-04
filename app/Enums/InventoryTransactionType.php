<?php

namespace App\Enums;

enum InventoryTransactionType: string
{
    case SUPPLY = 'supply';
    case SALE = 'sale';
    case RETURN = 'return';
    case DAMAGE = 'damage';
    case ADJUSTMENT = 'adjustment';

    public function label(): string
    {
        return match ($this) {
            self::SUPPLY => 'Supply',
            self::SALE => 'Sale',
            self::RETURN => 'Return',
            self::DAMAGE => 'Damage',
            self::ADJUSTMENT => 'Adjustment',
        };
    }

    public function affectsStockPositively(): bool
    {
        return in_array($this, [self::SUPPLY, self::RETURN, self::ADJUSTMENT]);
    }

    public function affectsStockNegatively(): bool
    {
        return in_array($this, [self::SALE, self::DAMAGE]);
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function labels(): array
    {
        return array_reduce(
            self::cases(),
            fn($carry, $case) => $carry + [$case->value => $case->label()],
            []
        );
    }
}
