<?php

namespace App\Models\Traits;

trait HasActiveStatus
{
    /**
     * Scope a query to only include active items.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive items.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Activate this item.
     */
    public function activate()
    {
        return $this->update(['is_active' => true]);
    }

    /**
     * Deactivate this item.
     */
    public function deactivate()
    {
        return $this->update(['is_active' => false]);
    }

    /**
     * Toggle the active status.
     */
    public function toggleActive()
    {
        return $this->update(['is_active' => !$this->is_active]);
    }

    /**
     * Check if the item is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Check if the item is inactive.
     */
    public function isInactive(): bool
    {
        return !$this->is_active;
    }
} 