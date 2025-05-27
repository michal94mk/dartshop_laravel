<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PrivacyPolicy extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'version',
        'is_active',
        'effective_date',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'effective_date' => 'datetime',
    ];

    /**
     * Get the currently active privacy policy.
     */
    public static function getActive()
    {
        return self::where('is_active', true)
            ->where('effective_date', '<=', now())
            ->orderBy('effective_date', 'desc')
            ->first();
    }

    /**
     * Set this policy as active and deactivate others.
     */
    public function setAsActive()
    {
        // Deactivate all other policies
        self::where('id', '!=', $this->id)->update(['is_active' => false]);
        
        // Activate this policy
        $this->update(['is_active' => true]);
    }

    /**
     * Scope for active policies.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for current policies (effective now).
     */
    public function scopeCurrent($query)
    {
        return $query->where('effective_date', '<=', now());
    }
}
