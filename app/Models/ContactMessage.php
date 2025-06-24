<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    
    /**
     * Get status label for display
     *
     * @return string
     */
    public function getStatusLabelAttribute()
    {
        return $this->is_read ? 'Przeczytana' : 'Nieprzeczytana';
    }
    
    /**
     * Get status color class for display
     *
     * @return string
     */
    public function getStatusColorAttribute()
    {
        return $this->is_read ? 'green' : 'yellow';
    }
    
    /**
     * Mark message as read
     *
     * @return void
     */
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now()
        ]);
    }
    
    /**
     * Scope for unread messages
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }
    
    /**
     * Scope for read messages
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }
}
