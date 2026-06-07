<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\NotificationResource;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    public function index(): JsonResponse
    {
        $notifications = Notification::with(['user', 'routine'])
            ->orderByDesc('notification_time')
            ->get();

        return response()->json([
            'success' => true,
            'data' => NotificationResource::collection($notifications),
        ]);
    }

    public function show(Notification $notification): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new NotificationResource(
                $notification->load(['user', 'routine'])
            ),
        ]);
    }
}
