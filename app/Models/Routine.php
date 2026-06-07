<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Routine extends Model
{
    use HasFactory;

    protected $table = 'routines';
    protected $primaryKey = 'routine_id';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'routine_name',
        'routine_time',
        'created_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function steps(): HasMany
    {
        return $this->hasMany(RoutineStep::class, 'routine_id', 'routine_id');
    }
}
