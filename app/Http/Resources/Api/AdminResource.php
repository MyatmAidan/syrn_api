<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'admin_id' => $this->admin_id,
            'admin_name' => $this->admin_name,
            'email' => $this->email,
            'created_at' => $this->created_at,
        ];
    }
}
