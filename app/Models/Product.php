<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Traits\HasActiveStatus;
use App\Models\Traits\HasPromotions;
use Illuminate\Support\Facades\Storage;

/**
 *
 */

class Product extends Model
{
    use HasFactory, HasActiveStatus, HasPromotions;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'image_url',
        'is_featured',
        'is_active',
        'brand_id',
        'category_id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];
    
    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = ['average_rating', 'reviews_count'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }
    
    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }
    
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
    
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_favorite_products')
            ->withTimestamps();
    }

    /**
     * Get properly formatted image URL using ImageService
     */
    public function getImageUrlAttribute(): ?string
    {
        $imageUrl = $this->attributes['image_url'] ?? null;
        
        if (!$imageUrl) {
            return null;
        }
        
        // If it's a full URL (http/https)
        if (str_starts_with($imageUrl, 'http://') || str_starts_with($imageUrl, 'https://')) {
            return $imageUrl;
        }
        
        // Normalize path separators
        $path = str_replace('\\', '/', $imageUrl);
        
        // Remove any leading slashes
        $path = ltrim($path, '/');
        
        // If path starts with storage/
        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, 8); // remove 'storage/'
        }
        
        // If path starts with products/ or is just a filename
        if (str_starts_with($path, 'products/') || !str_contains($path, '/')) {
            // Check if file exists in storage/app/public/products
            $storagePath = str_starts_with($path, 'products/') ? $path : 'products/' . $path;
            
            if (Storage::disk('public')->exists($storagePath)) {
                $url = url('storage/' . $storagePath);
                return $url;
            }
        }
        
        // Check if file exists in public/img
        $imgPath = 'img/' . basename($path);
        $publicPath = public_path($imgPath);
        
        if (file_exists($publicPath)) {
            $url = url($imgPath);
            return $url;
        }
        
        // If file not found, return path to storage/products
        $fallbackUrl = url('storage/products/' . basename($path));
        return $fallbackUrl;
    }

    public function approvedReviews(): HasMany
    {
        return $this->reviews()->where('is_approved', true);
    }

    public function getAverageRatingAttribute(): float
    {
        return round($this->approvedReviews()->avg('rating') ?? 0, 1);
    }

    public function getReviewsCountAttribute(): int
    {
        return $this->approvedReviews()->count();
    }

    /**
     * Get distribution of review ratings
     *
     * @return array
     */
    public function getReviewsDistribution(): array
    {
        $distribution = [];
        
        for ($i = 1; $i <= 5; $i++) {
            $distribution[$i] = $this->reviews()->where('rating', $i)->where('is_approved', true)->count();
        }
        
        return $distribution;
    }

    /**
     * Check if user can review this product (must be authenticated and not have reviewed before)
     */
    public function canBeReviewedBy($userId): bool
    {
        if (!$userId) {
            return false;
        }
        
        return !$this->reviews()->where('user_id', $userId)->exists();
    }
}
