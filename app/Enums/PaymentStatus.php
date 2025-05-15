<?php

namespace App\Enums;

/**
 * Enum representing the various statuses a payment can have in the system.
 * 
 * This enum defines all possible states of a payment transaction,
 * tracking its progression from initiation to completion or failure.
 */
enum PaymentStatus: string
{
    /**
     * Payment has been initiated but not yet processed
     */
    case PENDING = 'pending';
    
    /**
     * Payment is currently being processed by the payment gateway
     */
    case PROCESSING = 'processing';
    
    /**
     * Payment has been successfully completed
     */
    case COMPLETED = 'completed';
    
    /**
     * Payment has failed
     */
    case FAILED = 'failed';
    
    /**
     * Payment has been refunded to the customer
     */
    case REFUNDED = 'refunded';

    /**
     * Get a human-readable label for the payment status.
     *
     * @return string The localized display name for the status
     */
    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pending',
            self::PROCESSING => 'Processing',
            self::COMPLETED => 'Completed',
            self::FAILED => 'Failed',
            self::REFUNDED => 'Refunded',
        };
    }
    
    /**
     * Get a human-readable label for the payment status in Polish.
     * 
     * @return string The Polish display name for the status
     */
    public function labelPl(): string
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
