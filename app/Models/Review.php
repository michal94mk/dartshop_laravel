<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Traits\BelongsToUser;
use App\Models\Traits\HasApprovalStatus;

/**
 *
 */

class Review extends Model
{
    use HasFactory, BelongsToUser, HasApprovalStatus;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'product_id',
        'rating',
        'content',
        'title',
        'is_approved',
        'is_featured'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'rating' => 'integer',
        'is_approved' => 'boolean',
        'is_featured' => 'boolean',
    ];
    
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }
    
    public function scopeApprovedAndFeatured(Builder $query): Builder
    {
        return $query->where('is_approved', true)->where('is_featured', true);
    }
    
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
