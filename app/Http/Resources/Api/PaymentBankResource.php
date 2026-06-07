<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PaymentBankResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'payment_bank_id' => $this->payment_bank_id,
            'bank_name' => $this->bank_name,
            'account_name' => $this->account_name,
            'account_number' => $this->account_number,
            'qr_image' => $this->qr_image,
            'qr_image_url' => $this->qr_image
                ? (str_starts_with($this->qr_image, 'http') ? $this->qr_image : Storage::disk('public')->url($this->qr_image))
                : null,
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
