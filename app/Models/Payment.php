<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\BelongsToUser;

/**
 * Payment model representing payment transactions in the e-commerce system.
 * 
 * This model tracks payment information including method, amount, status,
 * and transaction details. It provides a record of all payment operations
 * and their current status associated with orders.
 */
class Payment extends Model
{
    use HasFactory, BelongsToUser;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'user_id',
        'payment_method',
        'payment_status',
        'amount',
        'currency',
        'transaction_id',
        'payment_intent_id',
        'payment_data',
        'paid_at',
        'failure_reason'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'payment_status' => PaymentStatus::class,
        'payment_data' => 'json',
        'paid_at' => 'datetime'
    ];

    /**
     * Get the order associated with the payment.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
