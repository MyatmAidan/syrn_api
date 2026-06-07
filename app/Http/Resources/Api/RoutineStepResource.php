<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoutineStepResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'step_id' => $this->step_id,
            'routine_id' => $this->routine_id,
            'product_id' => $this->product_id,
            'product' => new ProductResource($this->whenLoaded('product')),
            'step_order' => $this->step_order,
            'instruction' => $this->instruction,
        ];
    }
}
