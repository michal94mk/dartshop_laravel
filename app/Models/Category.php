<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'description', 
        'image', 
        'slug', 
        'sort_order', 
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected $appends = ['image_url', 'products_count'];

    /**
     * Relacja z produktami
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Tylko aktywne produkty
     */
    public function activeProducts()
    {
        return $this->hasMany(Product::class)->where('is_active', true);
    }

    /**
     * Scope dla aktywnych kategorii
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope dla sortowania według sort_order
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('name', 'asc');
    }

    /**
     * Scope dla kategorii z produktami
     */
    public function scopeWithProducts(Builder $query): Builder
    {
        return $query->whereHas('activeProducts');
    }

    /**
     * Automatyczne generowanie slug przy zapisie
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = $category->generateUniqueSlug($category->name);
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('name') && empty($category->getOriginal('slug'))) {
                $category->slug = $category->generateUniqueSlug($category->name);
            }
        });
    }

    /**
     * Generuje unikalny slug
     */
    private function generateUniqueSlug($name)
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (static::where('slug', $slug)->where('id', '!=', $this->id)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Zwraca pełny URL obrazka
     */
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
        
        // Sprawdź czy plik istnieje w storage/categories
        $storagePath = 'storage/categories/' . $this->image;
        if (file_exists(public_path($storagePath))) {
            return asset($storagePath);
        }
        
        // Sprawdź czy plik istnieje w img/categories
        $imgPath = 'img/categories/' . $this->image;
        if (file_exists(public_path($imgPath))) {
            return asset($imgPath);
        }
        
        // Fallback - spróbuj storage
        return asset('storage/' . $this->image);
    }

    /**
     * Zwraca liczbę aktywnych produktów w kategorii
     */
    public function getProductsCountAttribute(): int
    {
        return $this->activeProducts()->count();
    }

    /**
     * Znajdź kategorię po slug
     */
    public static function findBySlug($slug)
    {
        return static::where('slug', $slug)->first();
    }

    /**
     * Walidacja
     */
    public static function rules($id = null)
    {
        $rules = [
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug',
            'sort_order' => 'integer|min:0',
            'is_active' => 'boolean',
        ];

        if ($id) {
            $rules['name'] = [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name')->ignore($id),
            ];
            $rules['slug'] = [
                'nullable',
                'string',
                'max:255',
                Rule::unique('categories', 'slug')->ignore($id),
            ];
        }
        return $rules;
    }
}
