<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 *
 */

class ContactMessage extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'subject',
        'message',
        'is_read',
        'read_at'
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];
    
    public function getStatusLabelAttribute(): string
    {
        return $this->is_read ? 'Przeczytana' : 'Nieprzeczytana';
    }
    
    public function getStatusColorAttribute(): string
    {
        return $this->is_read ? 'green' : 'yellow';
    }
    
    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now()
        ]);
    }
    
    public function scopeUnread($query): Builder
    {
        return $query->where('is_read', false);
    }
    
    public function scopeRead($query): Builder
    {
        return $query->where('is_read', true);
    }
}
