<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'price', 'weight', 'category_id', 'brand_id', 'image', 'stock', 'featured', 'is_active'];

    protected $casts = [
        'price' => 'decimal:2',
        'weight' => 'decimal:2',
        'featured' => 'boolean',
        'is_active' => 'boolean',
    ];
    
    protected $appends = ['image_url'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
    
    public function carts()
    {
        return $this->hasMany(CartItem::class);
    }
    
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function getImageUrlAttribute()
    {
        // Check if the product already has an image_url attribute (e.g., from mock data)
        if (isset($this->attributes['image_url'])) {
            return $this->attributes['image_url'];
        }
        
        // Check if the product has an image field
        if (!empty($this->image)) {
            // Check if it's a full URL
            if (filter_var($this->image, FILTER_VALIDATE_URL)) {
                return $this->image;
            }
            
            // Check if it's an absolute path to a public file
            if (strpos($this->image, '/') === 0) {
                return $this->image;
            }
            
            // Otherwise, treat it as a relative path in storage
            return asset('storage/' . $this->image);
        }
        
        // Default image when no image is provided
        return asset('images/default-product.jpg');
    }    
}
