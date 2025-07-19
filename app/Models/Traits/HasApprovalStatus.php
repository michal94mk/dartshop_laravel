<?php

namespace App\Models\Traits;

trait HasApprovalStatus
{
    /**
     * Scope a query to only include approved items.
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope a query to only include pending (not approved) items.
     */
    public function scopePending($query)
    {
        return $query->where('is_approved', false);
    }

    /**
     * Approve this item.
     */
    public function approve()
    {
        $updateData = ['is_approved' => true];
        
        // Add approved_at timestamp if column exists
        if ($this->isFillable('approved_at') || array_key_exists('approved_at', $this->getCasts())) {
            $updateData['approved_at'] = now();
        }
        
        return $this->update($updateData);
    }

    /**
     * Reject (disapprove) this item.
     */
    public function reject()
    {
        $updateData = ['is_approved' => false];
        
        // Clear approved_at timestamp if column exists
        if ($this->isFillable('approved_at') || array_key_exists('approved_at', $this->getCasts())) {
            $updateData['approved_at'] = null;
        }
        
        return $this->update($updateData);
    }

    /**
     * Toggle the approval status.
     */
    public function toggleApproval()
    {
        if ($this->is_approved) {
            return $this->reject();
        } else {
            return $this->approve();
        }
    }

    /**
     * Check if this item is approved.
     */
    public function isApproved(): bool
    {
        return $this->is_approved;
    }

    /**
     * Check if this item is pending approval.
     */
    public function isPending(): bool
    {
        return !$this->is_approved;
    }
} 