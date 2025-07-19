<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tutorial extends Model
{
    use HasFactory;

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
     * Boot function from Laravel.
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($tutorial) {
            if (empty($tutorial->slug)) {
                $tutorial->slug = Str::slug($tutorial->title);
            }
        });
        
        static::updating(function ($tutorial) {
            if (empty($tutorial->slug)) {
                $tutorial->slug = Str::slug($tutorial->title);
            }
        });
    }

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
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
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
}
