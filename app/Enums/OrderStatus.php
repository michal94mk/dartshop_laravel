<?php

namespace App\Enums;

/**
 * Enum representing the various statuses an order can have in the system.
 * 
 * This enum defines all possible states of an order through its lifecycle,
 * from initial creation to completion or cancellation.
 */
enum OrderStatus: string
{
    /**
     * Order has been placed but no action has been taken yet
     */
    case PENDING = 'pending';
    
    /**
     * Order is being processed by the staff
     */
    case PROCESSING = 'processing';
    
    /**
     * Order has been processed and is ready for shipping
     */
    case COMPLETED = 'completed';
    
    /**
     * Order has been shipped to the customer
     */
    case SHIPPED = 'shipped';
    
    /**
     * Order has been delivered to the customer
     */
    case DELIVERED = 'delivered';
    
    /**
     * Order has been cancelled
     */
    case CANCELLED = 'cancelled';
    
    /**
     * Order has been refunded
     */
    case REFUNDED = 'refunded';

    /**
     * Get a human-readable label for the order status.
     *
     * @return string The localized display name for the status
     */
    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pending',
            self::PROCESSING => 'Processing',
            self::COMPLETED => 'Completed',
            self::SHIPPED => 'Shipped',
            self::DELIVERED => 'Delivered',
            self::CANCELLED => 'Cancelled',
            self::REFUNDED => 'Refunded',
        };
    }
    
    /**
     * Get a human-readable label for the order status in Polish.
     * 
     * @return string The Polish display name for the status
     */
    public function labelPl(): string
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
