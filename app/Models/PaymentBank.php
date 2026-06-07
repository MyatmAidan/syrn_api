<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentBank extends Model
{
    protected $table = 'payment_banks';
    protected $primaryKey = 'payment_bank_id';

    protected $fillable = [
        'bank_name',
        'account_name',
        'account_number',
        'qr_image',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function orderPayments(): HasMany
    {
        return $this->hasMany(OrderPayment::class, 'payment_bank_id', 'payment_bank_id');
    }
}
