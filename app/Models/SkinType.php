<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SkinType extends Model
{
    protected $table = 'skin_types';
    protected $primaryKey = 'skin_type_id';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
        'created_at',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'skin_type_id', 'skin_type_id');
    }
}
