<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';
    case CANCELLED = 'cancelled';
    case REFUNDED = 'refunded';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Oczekujące',
            self::PROCESSING => 'W trakcie realizacji',
            self::COMPLETED => 'Zrealizowane',
            self::SHIPPED => 'Wysłane',
            self::DELIVERED => 'Dostarczone',
            self::CANCELLED => 'Anulowane',
            self::REFUNDED => 'Zwrócone',
        };
    }
}
