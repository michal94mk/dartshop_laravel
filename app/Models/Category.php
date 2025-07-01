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
        'slug',
        'description',
        'image',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $appends = ['products_count'];

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
        return $this->hasMany(Product::class);
    }

    /**
     * Scope dla sortowania według nazwy
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('name', 'asc');
    }

    /**
     * Scope dla kategorii z produktami
     */
    public function scopeWithProducts(Builder $query): Builder
    {
        return $query->whereHas('products');
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
     * Zwraca liczbę produktów w kategorii
     */
    public function getProductsCountAttribute(): int
    {
        return $this->products()->count();
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
            'slug' => 'nullable|string|max:255|unique:categories,slug',
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
