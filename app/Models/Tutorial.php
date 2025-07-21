<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Traits\HasSlug;
use Illuminate\Database\Eloquent\Builder;

/**
 *
 */

class Tutorial extends Model
{
    use HasFactory, HasSlug;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'slug',
        'content',
        'image_url',
        'order',
        'is_published'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_published' => 'boolean',
        'order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function scopePublished($query): Builder
    {
        return $query->where('is_published', true);
    }

    public function getExcerptAttribute(): string
    {
        return Str::limit(strip_tags($this->content), 150);
    }

    public function getFeaturedImageUrlAttribute(): string
    {
        if ($this->image_url) {
            // Check if it's already a full URL
            if (str_starts_with($this->image_url, 'http')) {
                return $this->image_url;
            }
            
            // Check if it's a seeder image (starts with 'img/')
            if (str_starts_with($this->image_url, 'img/')) {
                return asset($this->image_url);
            }
            
            // If it's a storage path, construct the proper URL
            return asset('storage/' . $this->image_url);
        }
        return asset('img/tutorials/dart-basics-beginners.jpg');
    }

    public function getThumbnailImageUrlAttribute(): string
    {
        if ($this->image_url) {
            // Check if it's already a full URL
            if (str_starts_with($this->image_url, 'http')) {
                return $this->image_url;
            }
            
            // Check if it's a seeder image (starts with 'img/')
            if (str_starts_with($this->image_url, 'img/')) {
                return asset($this->image_url);
            }
            
            // If it's a storage path, construct the proper URL
            return asset('storage/' . $this->image_url);
        }
        return asset('img/tutorials/dart-basics-beginners.jpg');
    }

    public function user(): object
    {
        return (object) ['name' => 'DartShop Admin'];
    }

    /**
     * Get the source value for slug generation (required by HasSlug trait).
     */
    public function getSlugSource(): string
    {
        return $this->title;
    }
}
