<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'price', 'weight', 'category_id', 'brand_id', 'image'];

    protected $casts = [
        'price' => 'decimal:2',
        'weight' => 'decimal:2',
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
        // Sprawdź, czy produkt ma już pole image_url (np. z mock danych)
        if (isset($this->attributes['image_url'])) {
            return $this->attributes['image_url'];
        }
        
        // Sprawdź, czy produkt ma pole image
        if (!empty($this->image)) {
            // Sprawdź, czy to pełny URL
            if (filter_var($this->image, FILTER_VALIDATE_URL)) {
                return $this->image;
            }
            
            // Sprawdź, czy to ścieżka bezwzględna do pliku publicznego
            if (strpos($this->image, '/') === 0) {
                return $this->image;
            }
            
            // W przeciwnym razie traktuj jako relatywny path w storage
            return asset('storage/' . $this->image);
        }
        
        // Domyślny obrazek, gdy brak zdjęcia
        return asset('images/default-product.jpg');
    }
}
