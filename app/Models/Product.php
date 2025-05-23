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

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }
}
