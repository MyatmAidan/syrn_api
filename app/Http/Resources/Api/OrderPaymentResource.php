<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class OrderPaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'order_payment_id' => $this->order_payment_id,
            'order_id' => $this->order_id,
            'payment_bank_id' => $this->payment_bank_id,
            'payment_bank' => new PaymentBankResource($this->whenLoaded('paymentBank')),
            'amount' => $this->amount,
            'slip_image' => $this->slip_image,
            'slip_image_url' => $this->slip_image
                ? (str_starts_with($this->slip_image, 'http') ? $this->slip_image : Storage::disk('public')->url($this->slip_image))
                : null,
            'status' => $this->status?->value ?? $this->status,
            'reviewed_by_admin_id' => $this->reviewed_by_admin_id,
            'admin_note' => $this->admin_note,
            'reviewed_at' => $this->reviewed_at,
            'created_at' => $this->created_at,
        ];
    }
}
