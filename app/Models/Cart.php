<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'quantity',
        // Dodaj inne kolumny, jeśli są potrzebne
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
