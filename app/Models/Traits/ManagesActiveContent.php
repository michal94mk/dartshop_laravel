<?php

namespace App\Models\Traits;

trait ManagesActiveContent
{
    /**
     * Get the currently active item.
     */
    public static function getActive()
    {
        return self::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->first();
    }

    /**
     * Set this item as active and deactivate others.
     */
    public function setAsActive()
    {
        // Deactivate all other items
        static::where('id', '!=', $this->id)->update(['is_active' => false]);
        
        // Activate this item
        $this->update(['is_active' => true]);
    }

    /**
     * Scope for active items.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
} 