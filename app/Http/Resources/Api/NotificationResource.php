<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'notification_id' => $this->notification_id,
            'user_id' => $this->user_id,
            'user' => new UserResource($this->whenLoaded('user')),
            'routine_id' => $this->routine_id,
            'routine' => new RoutineResource($this->whenLoaded('routine')),
            'message' => $this->message,
            'notification_time' => $this->notification_time,
            'status' => $this->status,
        ];
    }
}
