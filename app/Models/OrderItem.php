<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 *
 */

class OrderItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'quantity',
        'product_price',
        'total_price'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'price',
        'total',
        'price_formatted',
        'total_formatted'
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the price attribute (alias for product_price).
     */
    public function getPriceAttribute()
    {
        return $this->product_price;
    }

    /**
     * Get the total attribute (alias for total_price).
     */
    public function getTotalAttribute()
    {
        return $this->total_price;
    }

    /**
     * Get the formatted price.
     */
    public function getPriceFormattedAttribute()
    {
        return number_format($this->product_price, 2, ',', ' ') . ' zł';
    }

    /**
     * Get the formatted total.
     */
    public function getTotalFormattedAttribute()
    {
        return number_format($this->total_price, 2, ',', ' ') . ' zł';
    }
}
