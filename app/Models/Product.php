<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Traits\HasActiveStatus;
use App\Models\Traits\HasPromotions;

class Product extends Model
{
    use HasFactory, HasActiveStatus, HasPromotions;

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

    protected $casts = [
        'price' => 'decimal:2',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];
    
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
            \Illuminate\Support\Facades\Log::debug('Product image is null', [
                'product_id' => $this->id,
                'product_name' => $this->name
            ]);
            return null;
        }
        
        \Illuminate\Support\Facades\Log::debug('Processing product image', [
            'product_id' => $this->id,
            'product_name' => $this->name,
            'original_image_url' => $imageUrl
        ]);
        
        // Jeśli to pełny URL (http/https)
        if (str_starts_with($imageUrl, 'http://') || str_starts_with($imageUrl, 'https://')) {
            return $imageUrl;
        }
        
        // Normalizuj separatory ścieżek
        $path = str_replace('\\', '/', $imageUrl);
        
        // Usuń ewentualne początkowe slashe
        $path = ltrim($path, '/');
        
        // Jeśli ścieżka zaczyna się od storage/
        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, 8); // usuń 'storage/'
        }
        
        // Jeśli ścieżka zaczyna się od products/ lub jest samą nazwą pliku
        if (str_starts_with($path, 'products/') || !str_contains($path, '/')) {
            // Sprawdź czy plik istnieje w storage/app/public/products
            $storagePath = str_starts_with($path, 'products/') ? $path : 'products/' . $path;
            
            \Illuminate\Support\Facades\Log::debug('Checking storage path', [
                'product_id' => $this->id,
                'storage_path' => $storagePath,
                'full_path' => \Illuminate\Support\Facades\Storage::disk('public')->path($storagePath),
                'exists' => \Illuminate\Support\Facades\Storage::disk('public')->exists($storagePath)
            ]);
            
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($storagePath)) {
                $url = url('storage/' . $storagePath);
                \Illuminate\Support\Facades\Log::debug('Found file in storage', [
                    'product_id' => $this->id,
                    'storage_path' => $storagePath,
                    'url' => $url
                ]);
                return $url;
            }
        }
        
        // Sprawdź czy plik istnieje w public/img
        $imgPath = 'img/' . basename($path);
        $publicPath = public_path($imgPath);
        
        \Illuminate\Support\Facades\Log::debug('Checking public path', [
            'product_id' => $this->id,
            'img_path' => $imgPath,
            'public_path' => $publicPath,
            'exists' => file_exists($publicPath)
        ]);
        
        if (file_exists($publicPath)) {
            $url = url($imgPath);
            \Illuminate\Support\Facades\Log::debug('Found file in public/img', [
                'product_id' => $this->id,
                'img_path' => $imgPath,
                'url' => $url
            ]);
            return $url;
        }
        
        // Jeśli nie znaleziono pliku, zwróć ścieżkę do storage/products
        $fallbackUrl = url('storage/products/' . basename($path));
        \Illuminate\Support\Facades\Log::debug('Using fallback URL', [
            'product_id' => $this->id,
            'fallback_url' => $fallbackUrl,
            'original_path' => $path
        ]);
        return $fallbackUrl;
    }

    /**
     * Get approved reviews for this product
     */
    public function approvedReviews(): HasMany
    {
        return $this->reviews()->where('is_approved', true);
    }

    /**
     * Get average rating for this product
     */
    public function getAverageRatingAttribute(): float
    {
        return round($this->approvedReviews()->avg('rating') ?? 0, 1);
    }

    /**
     * Get total number of approved reviews
     */
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
