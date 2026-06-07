<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoutineResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'routine_id' => $this->routine_id,
            'user_id' => $this->user_id,
            'routine_name' => $this->routine_name,
            'routine_time' => $this->routine_time,
            'steps' => RoutineStepResource::collection($this->whenLoaded('steps')),
            'user' => new UserResource($this->whenLoaded('user')),
            'created_at' => $this->created_at,
        ];
    }
}
