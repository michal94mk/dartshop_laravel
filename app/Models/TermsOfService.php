<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\ManagesActiveContent;

class TermsOfService extends Model
{
    use HasFactory, ManagesActiveContent;

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
}
