<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'city',
        'postal_code',
        'country',
        'notes',
        'subtotal',
        'shipping_cost',
        'discount',
        'total',
        'payment_method',
        'session_id',
        'promotion_code'
    ];

    protected $casts = [
        'status' => OrderStatus::class,
    ];

    /**
     * Get the user that owns the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the items for the order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the payment for the order.
     */
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * Generate a unique order number.
     */
    public static function generateOrderNumber(): string
    {
        $lastOrder = self::orderBy('id', 'desc')->first();
        $lastId = $lastOrder ? $lastOrder->id : 0;
        return 'ZAM-' . str_pad($lastId + 1, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Get the full name of the customer.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get the full address of the customer.
     */
    public function getFullAddressAttribute(): string
    {
        return "{$this->address}, {$this->postal_code} {$this->city}, {$this->country}";
    }
}
