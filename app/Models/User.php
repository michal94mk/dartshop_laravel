<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Auth\Events\Registered;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


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
        'first_name',
        'last_name',
        'email',
        'email_verified_at',
        'password',
        'is_admin',
        'google_id',
        'avatar',
        'privacy_policy_accepted',
        'privacy_policy_accepted_at',
        'terms_of_service_accepted',
        'terms_of_service_accepted_at',
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
        'privacy_policy_accepted' => 'boolean',
        'privacy_policy_accepted_at' => 'datetime',
        'terms_of_service_accepted' => 'boolean',
        'terms_of_service_accepted_at' => 'datetime',
    ];
    
    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'role',
        'full_name',
        'display_name',
        'is_google_user',
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
    public function reviews(): HasMany
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
    public function favoriteProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'user_favorite_products')
            ->withTimestamps();
    }
    
    /**
     * Get the shipping addresses associated with the user.
     */
    public function shippingAddresses(): HasMany
    {
        return $this->hasMany(ShippingAddress::class);
    }
    
    /**
     * Get the user's full name.
     */
    public function getFullNameAttribute()
    {
        if ($this->first_name && $this->last_name) {
            return "{$this->first_name} {$this->last_name}";
        }
        
        return $this->name;
    }
    
    /**
     * Get the user's display name (full name if available, otherwise username).
     */
    public function getDisplayNameAttribute()
    {
        return $this->getFullNameAttribute();
    }
    
    /**
     * Get the user's role based on is_admin field.
     */
    public function getRoleAttribute()
    {
        return $this->is_admin ? 'admin' : 'user';
    }
    
    /**
     * Check if the user is logged in via Google OAuth.
     */
    public function getIsGoogleUserAttribute()
    {
        return !empty($this->google_id);
    }

    /**
     * Send the email verification notification (queued).
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new \App\Notifications\QueuedEmailVerificationNotification);
    }

    /**
     * Send the password reset notification (queued).
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\QueuedResetPasswordNotification($token));
    }
}
