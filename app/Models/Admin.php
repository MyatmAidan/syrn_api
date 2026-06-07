<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Admin extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'admins';
    protected $primaryKey = 'admin_id';

    public $timestamps = false;

    protected $fillable = [
        'admin_name',
        'email',
        'password_hash',
        'created_at',
    ];

    protected $hidden = [
        'password_hash',
    ];

    /**
     * Override standard Laravel password attribute.
     */
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'admin_id', 'admin_id');
    }
}
