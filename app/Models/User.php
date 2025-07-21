<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Notifications\QueuedEmailVerificationNotification;
use App\Notifications\QueuedResetPasswordNotification;

/**
 *
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
     * @var array<int, string>
     */
    protected $appends = [
        'role',
        'full_name',
        'display_name',
        'is_google_user',
    ];
    
    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }
    
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function favoriteProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'user_favorite_products')
            ->withTimestamps();
    }
    
    public function shippingAddresses(): HasMany
    {
        return $this->hasMany(ShippingAddress::class);
    }
    
    public function getFullNameAttribute(): string
    {
        if ($this->first_name && $this->last_name) {
            return "{$this->first_name} {$this->last_name}";
        }
        
        return $this->name;
    }
    
    public function getDisplayNameAttribute(): string
    {
        return $this->getFullNameAttribute();
    }
    
    public function getRoleAttribute(): string
    {
        return $this->is_admin ? 'admin' : 'user';
    }
    
    public function getIsGoogleUserAttribute(): bool
    {
        return !empty($this->google_id);
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new QueuedEmailVerificationNotification);
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new QueuedResetPasswordNotification($token));
    }
}
