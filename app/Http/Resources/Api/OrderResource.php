<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'order_id' => $this->order_id,
            'user_id' => $this->user_id,
            'order_number' => $this->order_number,
            'status' => $this->status?->value ?? $this->status,
            'subtotal' => $this->subtotal,
            'total' => $this->total,
            'shipping_name' => $this->shipping_name,
            'shipping_phone' => $this->shipping_phone,
            'shipping_address' => $this->shipping_address,
            'customer_note' => $this->customer_note,
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'payment' => new OrderPaymentResource($this->whenLoaded('payment')),
            'user' => $this->whenLoaded('user', fn () => [
                'user_id' => $this->user->user_id,
                'full_name' => $this->user->full_name,
                'email' => $this->user->email,
            ]),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
