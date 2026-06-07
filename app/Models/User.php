<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'user_id';

    public $timestamps = false; // Disabling standard created_at/updated_at timestamps

    protected $fillable = [
        'full_name',
        'email',
        'password_hash',
        'skin_type',
        'skin_concern',
        'profile_picture',
        'created_at',
    ];

    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    /**
     * Override standard Laravel password attribute.
     */
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function routines(): HasMany
    {
        return $this->hasMany(Routine::class, 'user_id', 'user_id');
    }

    public function recommendations(): HasMany
    {
        return $this->hasMany(Recommendation::class, 'user_id', 'user_id');
    }

    public function favourites(): HasMany
    {
        return $this->hasMany(Favourite::class, 'user_id', 'user_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'user_id', 'user_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'user_id', 'user_id');
    }

    public function cart(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Cart::class, 'user_id', 'user_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'user_id', 'user_id');
    }
}
