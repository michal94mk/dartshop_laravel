<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TermsOfService extends Model
{
    use HasFactory;

    protected $table = 'terms_of_service';

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
     * Get the currently active terms of service.
     */
    public static function getActive()
    {
        return self::where('is_active', true)
            ->orderBy('created_at', 'desc')
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
}
