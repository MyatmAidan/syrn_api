<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'review_id' => $this->review_id,
            'user_id' => $this->user_id,
            'product_id' => $this->product_id,
            'user' => new UserResource($this->whenLoaded('user')),
            'product' => new ProductResource($this->whenLoaded('product')),
            'rating' => $this->rating,
            'comment' => $this->comment,
            'review_date' => $this->review_date,
        ];
    }
}
