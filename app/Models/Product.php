<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';
    protected $primaryKey = 'product_id';

    public $timestamps = false;

    protected $fillable = [
        'category_id',
        'admin_id',
        'product_name',
        'brand_id',
        'ingredients',
        'skin_type_id',
        'skin_concern',
        'price',
        'qty',
        'description',
        'images',
        'created_at',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'qty' => 'integer',
            'images' => 'array',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id', 'brand_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'admin_id');
    }

    public function skinType(): BelongsTo
    {
        return $this->belongsTo(SkinType::class, 'skin_type_id', 'skin_type_id');
    }

    public function routineSteps(): HasMany
    {
        return $this->hasMany(RoutineStep::class, 'product_id', 'product_id');
    }

    public function recommendations(): HasMany
    {
        return $this->hasMany(Recommendation::class, 'product_id', 'product_id');
    }

    public function favourites(): HasMany
    {
        return $this->hasMany(Favourite::class, 'product_id', 'product_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'product_id', 'product_id');
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class, 'product_id', 'product_id');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'product_id', 'product_id');
    }
}
