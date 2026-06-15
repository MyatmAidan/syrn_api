<?php

namespace App\Http\Controllers\Api\User;

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
        $orders = Order::with(['items.product', 'payment.paymentBank', 'payment.reviewedBy', 'user'])
            ->where('user_id', $request->user()->user_id)
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => OrderResource::collection($orders),
        ]);
    }

    public function show(Request $request, Order $order): JsonResponse
    {
        $this->authorizeOrder($request, $order);

        return response()->json([
            'success' => true,
            'data' => new OrderResource(
                $order->load(['items.product', 'payment.paymentBank', 'payment.reviewedBy', 'user'])
            ),
        ]);
    }

    public function checkout(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'shipping_name' => 'required|string|max:150',
            'shipping_phone' => 'required|string|max:30',
            'shipping_address' => 'required|string',
            'customer_note' => 'nullable|string',
        ]);

        $order = $this->orderService->checkout($request->user()->user_id, $validated);

        return response()->json([
            'success' => true,
            'message' => 'Order placed. Please complete payment.',
            'data' => new OrderResource($order),
        ], 201);
    }

    public function submitPayment(Request $request, Order $order): JsonResponse
    {
        $this->authorizeOrder($request, $order);

        $validated = $request->validate([
            'payment_bank_id' => 'required|integer|exists:payment_banks,payment_bank_id',
            'slip_image' => 'required|image|max:5120',
        ]);

        $order = $this->orderService->submitPayment(
            $request->user()->user_id,
            $order->order_id,
            $validated['payment_bank_id'],
            $validated['slip_image']
        );

        return response()->json([
            'success' => true,
            'message' => 'Payment slip submitted. We will verify your payment shortly.',
            'data' => new OrderResource($order),
        ]);
    }

    protected function authorizeOrder(Request $request, Order $order): void
    {
        if ($order->user_id !== $request->user()->user_id) {
            abort(403, 'You do not have access to this order.');
        }
    }
}
