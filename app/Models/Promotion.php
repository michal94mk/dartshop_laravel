<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Carbon\Carbon;
use App\Models\Traits\HasActiveStatus;

/**
 * @OA\Schema(
 *     schema="Promotion",
 *     title="Promotion",
 *     description="Product promotion model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Summer Sale"),
 *     @OA\Property(property="description", type="string", example="Get 20% off on selected items"),
 *     @OA\Property(property="discount_type", type="string", enum={"percentage", "fixed"}, example="percentage"),
 *     @OA\Property(property="discount_value", type="number", format="float", example=20.0),
 *     @OA\Property(property="start_date", type="string", format="date-time"),
 *     @OA\Property(property="end_date", type="string", format="date-time"),
 *     @OA\Property(property="is_active", type="boolean", example=true),
 *     @OA\Property(property="badge_text", type="string", example="20% OFF"),
 *     @OA\Property(property="badge_color", type="string", example="#ff0000"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */

class Promotion extends Model
{
    use HasFactory, HasActiveStatus;

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
        'used_count',
        'badge_text',
        'badge_color',
        'is_featured',
        'display_order'
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
        'is_featured' => 'boolean',
        'display_order' => 'integer'
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
     * Scope dla aktywnych promocji (enhanced version from trait)
     * Overrides the basic active scope to include date validation
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
        return $query->where('is_featured', true);
    }

    /**
     * Scope dla sortowania według kolejności wyświetlania
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order', 'asc')->orderBy('created_at', 'desc');
    }

    /**
     * Scope dla promocji z produktami
     */
    public function scopeWithProducts($query)
    {
        return $query->with(['products:id,name,price,image_url']);
    }

    /**
     * Sprawdza czy promocja jest aktywna (enhanced version)
     * Overrides the basic isActive method to include date validation
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
     * Get badge text with fallback.
     */
    public function getBadgeTextAttribute()
    {
        if (isset($this->attributes['badge_text']) && $this->attributes['badge_text']) {
            return $this->attributes['badge_text'];
        }
        
        return $this->discount_type === 'percentage' 
            ? "-{$this->discount_value}%" 
            : "-{$this->discount_value} PLN";
    }

    /**
     * Get badge color with fallback.
     */
    public function getBadgeColorAttribute()
    {
        return isset($this->attributes['badge_color']) ? $this->attributes['badge_color'] : '#ff0000';
    }
}
