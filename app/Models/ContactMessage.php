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
        'reply',
        'status'
    ];
    
    /**
     * Get status label for display
     *
     * @return string
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'unread' => 'Nieprzeczytana',
            'read' => 'Przeczytana',
            'replied' => 'Odpowiedziano',
            default => 'Nieznany'
        };
    }
    
    /**
     * Get status color class for display
     *
     * @return string
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'unread' => 'yellow',
            'read' => 'blue',
            'replied' => 'green',
            default => 'gray'
        };
    }
}
