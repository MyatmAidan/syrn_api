<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Recommendation extends Model
{
    use HasFactory;

    protected $table = 'recommendations';
    protected $primaryKey = 'recommendation_id';

    const CREATED_AT = 'recommendation_date';
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'product_id',
        'reason',
        'recommendation_date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}
