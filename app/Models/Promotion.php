<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
        'title',
        'name',
        'description',
        'discount_type',
        'discount_value',
        'starts_at',
        'ends_at',
        'is_active',
        'code',
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
        'discount_value' => 'decimal:2'
    ];

    /**
     * Relacja many-to-many z produktami
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_promotions')
                    ->withTimestamps();
    }

    /**
     * Scope dla aktywnych promocji
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where('starts_at', '<=', now())
                    ->where(function ($q) {
                        $q->whereNull('ends_at')
                          ->orWhere('ends_at', '>=', now());
                    });
    }

    /**
     * Scope dla promocji wyróżnionych
     */
    public function scopeFeatured($query)
    {
        // Since we don't have is_featured field, return all active promotions
        return $query->where('is_active', true);
    }

    /**
     * Scope dla sortowania według kolejności wyświetlania
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Sprawdza czy promocja jest aktywna
     */
    public function isActive(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->starts_at > now()) {
            return false;
        }

        if ($this->ends_at && $this->ends_at < now()) {
            return false;
        }

        return true;
    }

    /**
     * Oblicza cenę po rabacie
     */
    public function calculateDiscountedPrice(float $originalPrice): float
    {
        if (!$this->isActive()) {
            return $originalPrice;
        }

        if ($this->discount_type === 'percentage') {
            return $originalPrice * (1 - ($this->discount_value / 100));
        }

        if ($this->discount_type === 'fixed') {
            return max(0, $originalPrice - $this->discount_value);
        }

        return $originalPrice;
    }

    /**
     * Zwraca kwotę rabatu
     */
    public function getDiscountAmount(float $originalPrice): float
    {
        return $originalPrice - $this->calculateDiscountedPrice($originalPrice);
    }

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
     * Increment the usage count
     *
     * @return bool
     */
    public function incrementUsage(): bool
    {
        $this->used_count += 1;
        return $this->save();
    }

    /**
     * Get default display order.
     */
    public function getDisplayOrderAttribute()
    {
        return 0;
    }

    /**
     * Get default is_featured value.
     */
    public function getIsFeaturedAttribute()
    {
        return true; // All active promotions are considered featured
    }

    /**
     * Get default badge text.
     */
    public function getBadgeTextAttribute()
    {
        return $this->discount_type === 'percentage' 
            ? "-{$this->discount_value}%" 
            : "-{$this->discount_value} PLN";
    }

    /**
     * Get default badge color.
     */
    public function getBadgeColorAttribute()
    {
        return 'red';
    }
}
