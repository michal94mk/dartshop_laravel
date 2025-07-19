<?php

namespace App\Models\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

trait BelongsToUser
{
    /**
     * Get the user that owns this item.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include items for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        if ($userId instanceof User) {
            $userId = $userId->id;
        }
        
        return $query->where('user_id', $userId);
    }

    /**
     * Check if this item belongs to a specific user.
     */
    public function belongsToUser(User $user): bool
    {
        return $this->user_id === $user->id;
    }

    /**
     * Check if this item belongs to the authenticated user.
     */
    public function belongsToAuthUser(): bool
    {
        return Auth::check() && $this->user_id === Auth::id();
    }
} 