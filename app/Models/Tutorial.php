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
        'featured_image',
        'thumbnail_image',
        'category',
        'difficulty',
        'is_published',
        'published_at',
        'meta_title',
        'meta_description',
        'excerpt',
        'user_id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
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
            
            if (empty($tutorial->excerpt) && !empty($tutorial->content)) {
                $tutorial->excerpt = Str::limit(strip_tags($tutorial->content), 150);
            }
        });
        
        static::updating(function ($tutorial) {
            if (empty($tutorial->slug)) {
                $tutorial->slug = Str::slug($tutorial->title);
            }
            
            if (empty($tutorial->excerpt) && !empty($tutorial->content)) {
                $tutorial->excerpt = Str::limit(strip_tags($tutorial->content), 150);
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
        return $query->where('is_published', true)
                     ->where('published_at', '<=', now());
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
     * Get the tutorial's featured image URL.
     *
     * @return string
     */
    public function getFeaturedImageUrlAttribute()
    {
        if (!$this->featured_image) {
            return asset('images/default-tutorial.jpg');
        }
        return asset('storage/images/' . $this->featured_image);
    }

    /**
     * Get the tutorial's thumbnail image URL.
     *
     * @return string
     */
    public function getThumbnailImageUrlAttribute()
    {
        if (!$this->thumbnail_image) {
            return asset('images/default-tutorial-thumbnail.jpg');
        }
        return asset('storage/images/' . $this->thumbnail_image);
    }

    /**
     * Get the user that authored the tutorial.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
