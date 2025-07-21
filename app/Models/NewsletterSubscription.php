<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;

/**
 *
 */

class NewsletterSubscription extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'status',
        'verification_token',
        'verified_at',
        'unsubscribed_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'verified_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
    ];

    public function generateVerificationToken(): string
    {
        $token = Str::random(64);
        $this->verification_token = $token;
        $this->save();
        
        return $token;
    }

    public function markAsVerified(): void
    {
        $this->status = 'active';
        $this->verified_at = now();
        $this->verification_token = null;
        $this->save();
    }

    public function unsubscribe(): void
    {
        $this->status = 'unsubscribed';
        $this->unsubscribed_at = now();
        $this->save();
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function scopeActive($query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopePending($query): Builder
    {
        return $query->where('status', 'pending');
    }
}
