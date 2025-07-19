<?php

namespace App\Models\Traits;

use Illuminate\Support\Str;

trait HasSlug
{
    /**
     * Boot the HasSlug trait for a model.
     */
    protected static function bootHasSlug()
    {
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = $model->generateSlug();
            }
        });
        
        static::updating(function ($model) {
            // Auto-regenerate slug only if the source field changed and slug is empty
            if ($model->isDirty($model->getSlugSourceField()) && empty($model->slug)) {
                $model->slug = $model->generateSlug();
            }
        });
    }

    /**
     * Generate a unique slug for this model.
     */
    public function generateSlug(): string
    {
        $baseSlug = Str::slug($this->getSlugSource());
        $slug = $baseSlug;
        $counter = 1;

        // Ensure uniqueness by adding numbers if necessary
        while (static::where('slug', $slug)->where('id', '!=', $this->id ?? 0)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Get the value that should be used for generating the slug.
     * This method should be implemented by the model using this trait.
     */
    abstract public function getSlugSource(): string;

    /**
     * Get the field name that is used as the source for the slug.
     * Override this method if your source field is not 'title'.
     */
    public function getSlugSourceField(): string
    {
        return 'title';
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Scope a query to find by slug.
     */
    public function scopeBySlug($query, string $slug)
    {
        return $query->where('slug', $slug);
    }
} 