<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
    case REFUNDED = 'refunded';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Oczekująca',
            self::PROCESSING => 'Przetwarzana',
            self::COMPLETED => 'Zakończona',
            self::FAILED => 'Nieudana',
            self::REFUNDED => 'Zwrócona',
        };
    }
}
