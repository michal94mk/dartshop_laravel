<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TermsOfService extends Model
{
    use HasFactory;

    protected $table = 'terms_of_service';

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
     * Get the currently active terms of service.
     */
    public static function getActive()
    {
        return self::where('is_active', true)
            ->where('effective_date', '<=', now())
            ->orderBy('effective_date', 'desc')
            ->first();
    }

    /**
     * Set this terms as active and deactivate others.
     */
    public function setAsActive()
    {
        // Deactivate all other terms
        self::where('id', '!=', $this->id)->update(['is_active' => false]);
        
        // Activate this terms
        $this->update(['is_active' => true]);
    }

    /**
     * Scope for active terms.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for current terms (effective now).
     */
    public function scopeCurrent($query)
    {
        return $query->where('effective_date', '<=', now());
    }
}
