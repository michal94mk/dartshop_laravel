<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Traits\HasSlug;

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

    /**
     * Scope a query to only include published tutorials.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Get excerpt from content (first 150 characters).
     */
    public function getExcerptAttribute()
    {
        return Str::limit(strip_tags($this->content), 150);
    }

    /**
     * Get featured image URL for tutorials.
     */
    public function getFeaturedImageUrlAttribute()
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

    /**
     * Get thumbnail image URL for tutorials.
     */
    public function getThumbnailImageUrlAttribute()
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

    /**
     * Get the user that authored the tutorial.
     * Since we don't have user_id in the table, return a default admin user object.
     */
    public function user()
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
