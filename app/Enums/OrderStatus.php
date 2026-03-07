<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PROCESSING = 'processing';
    case WITH_DELIVERY_COMPANY = 'with_delivery_company';
    case RECEIVED = 'received';
    case CANCELLED = 'cancelled';
    case RETURNED = 'returned';

    public function label(): string
    {
        return match ($this) {
            self::PROCESSING => 'Processing',
            self::WITH_DELIVERY_COMPANY => 'With Delivery Company',
            self::RECEIVED => 'Received',
            self::CANCELLED => 'Cancelled',
            self::RETURNED => 'Returned',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PROCESSING => 'blue',
            self::WITH_DELIVERY_COMPANY => 'yellow',
            self::RECEIVED => 'green',
            self::CANCELLED => 'red',
            self::RETURNED => 'orange',
        };
    }

    public function canTransitionTo(self $status): bool
    {
        return match ($this) {
            self::PROCESSING => in_array($status, [
                self::WITH_DELIVERY_COMPANY,
                self::RECEIVED,
                self::CANCELLED,
                self::RETURNED,
            ]),
            self::WITH_DELIVERY_COMPANY => in_array($status, [
                self::RECEIVED,
                self::CANCELLED,
                self::RETURNED,
            ]),
            self::RECEIVED => in_array($status, [
                self::CANCELLED,
                self::RETURNED,
            ]),
            self::CANCELLED => in_array($status, [
                self::PROCESSING,
            ]),
            self::RETURNED => in_array($status, [
                self::PROCESSING,
            ]),
        };
    }

    public function decreasesInventory(): bool
    {
        return $this === self::PROCESSING;
    }

    public function restoresInventory(): bool
    {
        return in_array($this, [self::CANCELLED, self::RETURNED]);
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
