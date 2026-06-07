<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SkinTypeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'skin_type_id' => $this->skin_type_id,
            'name' => $this->name,
            'description' => $this->description,
            'created_at' => $this->created_at,
        ];
    }
}
