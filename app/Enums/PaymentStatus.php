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
    case Pending = 'pending';
    case Processing = 'processing';
    case Completed = 'completed';
    case Paid = 'paid';
    case Failed = 'failed';
    case Refunded = 'refunded';
}
