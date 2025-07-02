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
        'video_url',
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
        return $this->image_url ? asset($this->image_url) : asset('img/tutorial-default.jpg');
    }

    /**
     * Get thumbnail image URL for tutorials.
     */
    public function getThumbnailImageUrlAttribute()
    {
        return $this->image_url ? asset($this->image_url) : asset('img/tutorial-thumbnail-default.jpg');
    }

    /**
     * Get default category.
     */
    public function getCategoryAttribute()
    {
        return 'Darts';
    }

    /**
     * Get default difficulty.
     */
    public function getDifficultyAttribute()
    {
        return 'Początkujący';
    }

    /**
     * Get published date (use created_at since we don't have published_at).
     */
    public function getPublishedAtAttribute()
    {
        return $this->created_at;
    }

    /**
     * Get meta title (use title if not set).
     */
    public function getMetaTitleAttribute()
    {
        return $this->title;
    }

    /**
     * Get meta description (use excerpt).
     */
    public function getMetaDescriptionAttribute()
    {
        return $this->excerpt;
    }

    /**
     * Get views count (default to 0 since we don't track views yet).
     */
    public function getViewsAttribute()
    {
        return 0;
    }

    /**
     * Get the user that authored the tutorial (return default admin user).
     */
    public function user()
    {
        // Since we don't have user_id in table, return a default admin user
        return (object) ['name' => 'DartShop Admin'];
    }
}
