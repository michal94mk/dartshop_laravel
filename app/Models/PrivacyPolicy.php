<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\ManagesActiveContent;

class PrivacyPolicy extends Model
{
    use HasFactory, ManagesActiveContent;

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
}
