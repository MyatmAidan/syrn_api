<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoutineStep extends Model
{
    use HasFactory;

    protected $table = 'routine_steps';
    protected $primaryKey = 'step_id';

    public $timestamps = false; // No timestamps at all on routine_steps

    protected $fillable = [
        'routine_id',
        'product_id',
        'step_order',
        'instruction',
    ];

    public function routine(): BelongsTo
    {
        return $this->belongsTo(Routine::class, 'routine_id', 'routine_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}
