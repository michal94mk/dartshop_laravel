<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CartItem[] $cartItems
 * @method \Illuminate\Database\Eloquent\Relations\HasMany cartItems()
 * @method \Illuminate\Database\Eloquent\Relations\HasMany reviews()
 * @method \Illuminate\Database\Eloquent\Relations\HasMany orders()
 * @method \Illuminate\Database\Eloquent\Relations\HasMany payments()
 * @method \Illuminate\Database\Eloquent\Relations\HasMany shippingAddresses()
 * @method \Illuminate\Database\Eloquent\Relations\BelongsToMany favoriteProducts()
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_admin' => 'boolean',
    ];
    
    /**
     * Get the cart items associated with the user.
     */
    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }
    
    /**
     * Get the reviews associated with the user.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get the orders associated with the user.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the payments associated with the user.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the favorite products of the user.
     */
    public function favoriteProducts()
    {
        return $this->belongsToMany(Product::class, 'user_favorite_products')
            ->withTimestamps();
    }
    
    /**
     * Get the shipping addresses associated with the user.
     */
    public function shippingAddresses()
    {
        return $this->hasMany(ShippingAddress::class);
    }
}
