<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrivacyPolicy extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'version',
        'effective_date',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'effective_date' => 'date',
    ];

    /**
     * Get the currently active privacy policy.
     */
    public static function getActive()
    {
        return self::where('is_active', true)
            ->orderBy('created_at', 'desc')
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
}
