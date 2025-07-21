<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Log;
use App\Models\Traits\BelongsToUser;

/**
 *
 */

class Order extends Model
{
    use HasFactory, BelongsToUser;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'payment_status',
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
        'payment_intent_id',
        'stripe_session_id',
        'shipping_method',
        'session_id',
        'promotion_code'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => OrderStatus::class,
        'subtotal' => 'float',
        'shipping_cost' => 'float',
        'discount' => 'float',
        'total' => 'float',
    ];
    
    protected static function boot()
    {
        parent::boot();

        // Auto-generate order number when creating a new order if not set
        static::creating(function ($order) {
            // Only generate if order_number is not already set
            if (empty($order->order_number)) {
                $order->order_number = self::generateOrderNumber();
            }
            
            // Log the order creation for debugging
            Log::info('Order creating event: ', [
                'order_number' => $order->order_number,
                'user_id' => $order->user_id,
                'email' => $order->email,
                'first_name' => $order->first_name,
                'last_name' => $order->last_name
            ]);
        });
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * Generate a unique order number.
     * 
     * Creates an order number in the format ZAM-XXXXXX where X is a sequential number.
     *
     * @return string
     */
    public static function generateOrderNumber(): string
    {
        $lastOrder = self::orderBy('id', 'desc')->first();
        $lastId = $lastOrder ? $lastOrder->id : 0;
        return 'ZAM-' . str_pad($lastId + 1, 6, '0', STR_PAD_LEFT);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getFullAddressAttribute(): string
    {
        return "{$this->address}, {$this->postal_code} {$this->city}, {$this->country}";
    }
}
