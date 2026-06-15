<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(protected OrderService $orderService) {}

    public function index(Request $request): JsonResponse
    {
        $query = Order::with(['items.product', 'payment.paymentBank', 'user']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->orderByDesc('created_at')->get();

        return response()->json([
            'success' => true,
            'data' => OrderResource::collection($orders),
        ]);
    }

    public function show(Order $order): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new OrderResource(
                $order->load(['items.product', 'payment.paymentBank', 'payment.reviewedBy', 'user'])
            ),
        ]);
    }

    public function approvePayment(Request $request, Order $order): JsonResponse
    {
        $validated = $request->validate([
            'admin_note' => 'nullable|string',
        ]);

        $order = $this->orderService->approvePayment(
            $order->order_id,
            $request->user()->admin_id,
            $validated['admin_note'] ?? null
        );

        return response()->json([
            'success' => true,
            'message' => 'Payment confirmed. Order is now confirmed.',
            'data' => new OrderResource($order),
        ]);
    }

    public function rejectPayment(Request $request, Order $order): JsonResponse
    {
        $validated = $request->validate([
            'admin_note' => 'nullable|string',
        ]);

        $order = $this->orderService->rejectPayment(
            $order->order_id,
            $request->user()->admin_id,
            $validated['admin_note'] ?? null
        );

        return response()->json([
            'success' => true,
            'message' => 'Payment rejected. Order cancelled.',
            'data' => new OrderResource($order),
        ]);
    }
}
