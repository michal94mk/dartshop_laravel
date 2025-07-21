<?php

namespace App\Models\Traits;

trait HasApprovalStatus
{
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopePending($query)
    {
        return $query->where('is_approved', false);
    }

    public function approve()
    {
        $updateData = ['is_approved' => true];
        
        // Add approved_at timestamp if column exists
        if ($this->isFillable('approved_at') || array_key_exists('approved_at', $this->getCasts())) {
            $updateData['approved_at'] = now();
        }
        
        return $this->update($updateData);
    }

    public function reject()
    {
        $updateData = ['is_approved' => false];
        
        // Clear approved_at timestamp if column exists
        if ($this->isFillable('approved_at') || array_key_exists('approved_at', $this->getCasts())) {
            $updateData['approved_at'] = null;
        }
        
        return $this->update($updateData);
    }

    public function toggleApproval()
    {
        if ($this->is_approved) {
            return $this->reject();
        } else {
            return $this->approve();
        }
    }

    public function isApproved(): bool
    {
        return $this->is_approved;
    }

    public function isPending(): bool
    {
        return !$this->is_approved;
    }
} 