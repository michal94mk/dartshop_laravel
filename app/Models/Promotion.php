<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Promotion extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
        'description',
        'discount_type',
        'discount_value',
        'minimum_order_value',
        'starts_at',
        'ends_at',
        'is_active',
        'usage_limit',
        'used_count'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
        'discount_value' => 'decimal:2',
        'minimum_order_value' => 'decimal:2',
        'usage_limit' => 'integer',
        'used_count' => 'integer',
    ];

    /**
     * Check if the promotion is currently valid (active and within date range)
     *
     * @return bool
     */
    public function isValid(): bool
    {
        $now = Carbon::now();
        
        // Check if promotion is active
        if (!$this->is_active) {
            return false;
        }
        
        // Check if promotion has started
        if ($now->lt($this->starts_at)) {
            return false;
        }
        
        // Check if promotion has ended (if end date is set)
        if ($this->ends_at && $now->gt($this->ends_at)) {
            return false;
        }
        
        // Check if usage limit has been reached (if limit is set)
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return false;
        }
        
        return true;
    }

    /**
     * Get the status label for display
     *
     * @return string
     */
    public function getStatusLabelAttribute(): string
    {
        if ($this->isValid()) {
            return 'Aktywna';
        }
        
        return 'Nieaktywna';
    }

    /**
     * Get the status color for display
     *
     * @return string
     */
    public function getStatusColorAttribute(): string
    {
        if ($this->isValid()) {
            return 'green';
        }
        
        return 'gray';
    }
    
    /**
     * Calculate discount amount for a given order value
     *
     * @param float $orderValue
     * @return float
     */
    public function calculateDiscountAmount(float $orderValue): float
    {
        // Check if minimum order value is met
        if ($this->minimum_order_value && $orderValue < $this->minimum_order_value) {
            return 0;
        }
        
        if ($this->discount_type === 'percentage') {
            return round($orderValue * ($this->discount_value / 100), 2);
        }
        
        // Fixed amount discount
        return min($this->discount_value, $orderValue);
    }

    /**
     * Increment the usage count
     *
     * @return bool
     */
    public function incrementUsage(): bool
    {
        $this->used_count += 1;
        return $this->save();
    }
}
