<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'user_id' => $this->user_id,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'skin_type' => $this->skin_type,
            'skin_concern' => $this->skin_concern,
            'profile_picture' => $this->profile_picture,
            'created_at' => $this->created_at,
        ];
    }
}
