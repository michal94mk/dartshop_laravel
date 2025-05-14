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
        'session_id',
        'user_id'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function getCurrentSessionId()
    {
        return Session::getId();
    }

    public static function getCartBySession($sessionId)
    {
        return self::where('session_id', $sessionId)->get();
    }

    public static function getCartByUser($userId)
    {
        return self::where('user_id', $userId)->get();
    }
}
