<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $items = CartItemResource::collection($this->whenLoaded('items'));
        $subtotal = 0;

        if ($this->relationLoaded('items')) {
            foreach ($this->items as $item) {
                if ($item->product && $item->product->price !== null) {
                    $subtotal += (float) $item->product->price * $item->quantity;
                }
            }
        }

        return [
            'cart_id' => $this->cart_id,
            'user_id' => $this->user_id,
            'items' => $items,
            'item_count' => $this->relationLoaded('items') ? $this->items->sum('quantity') : 0,
            'subtotal' => round($subtotal, 2),
            'updated_at' => $this->updated_at,
        ];
    }
}
