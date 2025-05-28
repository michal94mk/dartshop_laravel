<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'weight',
        'category_id',
        'brand_id',
        'image',
        'stock',
        'featured',
        'is_active'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'weight' => 'decimal:2',
        'featured' => 'boolean',
        'is_active' => 'boolean',
    ];
    
    protected $appends = ['image_url'];

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
     * Relacja many-to-many z promocjami
     */
    public function promotions(): BelongsToMany
    {
        return $this->belongsToMany(Promotion::class, 'product_promotions')
                    ->withTimestamps();
    }

    /**
     * Zwraca aktywne promocje dla produktu
     */
    public function activePromotions(): BelongsToMany
    {
        return $this->promotions()->active();
    }

    /**
     * Sprawdza czy produkt ma aktywną promocję
     */
    public function hasActivePromotion(): bool
    {
        return $this->activePromotions()->exists();
    }

    /**
     * Zwraca najlepszą aktywną promocję (największy rabat)
     */
    public function getBestActivePromotion(): ?Promotion
    {
        return $this->activePromotions()
                    ->get()
                    ->sortByDesc(function ($promotion) {
                        return $promotion->getDiscountAmount($this->price);
                    })
                    ->first();
    }

    /**
     * Zwraca cenę po rabacie (jeśli jest aktywna promocja)
     */
    public function getPromotionalPrice(): float
    {
        $bestPromotion = $this->getBestActivePromotion();
        
        if (!$bestPromotion) {
            return $this->price;
        }
        
        return $bestPromotion->calculateDiscountedPrice($this->price);
    }

    /**
     * Zwraca kwotę oszczędności
     */
    public function getSavingsAmount(): float
    {
        return $this->price - $this->getPromotionalPrice();
    }

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }
        
        // Jeśli obrazek już zawiera pełną ścieżkę storage
        if (str_starts_with($this->image, '/storage/') || str_starts_with($this->image, 'storage/')) {
            return asset($this->image);
        }
        
        // Jeśli to pełny URL (http/https)
        if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
            return $this->image;
        }
        
        // Sprawdź czy plik istnieje w storage/products
        $storagePath = 'storage/products/' . $this->image;
        if (file_exists(public_path($storagePath))) {
            return asset($storagePath);
        }
        
        // Sprawdź czy plik istnieje w img
        $imgPath = 'img/' . $this->image;
        if (file_exists(public_path($imgPath))) {
            return asset($imgPath);
        }
        
        // Fallback - spróbuj storage
        return asset('storage/' . $this->image);
    }
}
