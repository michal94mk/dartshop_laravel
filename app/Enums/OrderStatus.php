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
    case Pending = 'pending';
    case Processing = 'processing';
    case Shipped = 'shipped';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';
    case Refunded = 'refunded';
}
