<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'payment_id',
        'payment_method',
        'amount',
        'currency',
        'status',
        'transaction_data'
    ];

    protected $casts = [
        'status' => PaymentStatus::class,
        'transaction_data' => 'json'
    ];

    /**
     * Get the order associated with the payment.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the user associated with the payment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
