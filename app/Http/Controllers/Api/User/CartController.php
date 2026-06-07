<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CartResource;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(protected CartService $cartService) {}

    public function show(Request $request): JsonResponse
    {
        $cart = $this->cartService->getCartWithItems($request->user()->user_id);

        return response()->json([
            'success' => true,
            'data' => new CartResource($cart),
        ]);
    }

    public function addItem(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,product_id',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $cart = $this->cartService->addItem(
            $request->user()->user_id,
            $validated['product_id'],
            $validated['quantity'] ?? 1
        );

        return response()->json([
            'success' => true,
            'message' => 'Item added to cart.',
            'data' => new CartResource($cart),
        ]);
    }

    public function updateItem(Request $request, int $cartItem): JsonResponse
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = $this->cartService->updateItemQuantity(
            $request->user()->user_id,
            $cartItem,
            $validated['quantity']
        );

        return response()->json([
            'success' => true,
            'message' => 'Cart updated.',
            'data' => new CartResource($cart),
        ]);
    }

    public function removeItem(Request $request, int $cartItem): JsonResponse
    {
        $cart = $this->cartService->removeItem($request->user()->user_id, $cartItem);

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart.',
            'data' => new CartResource($cart),
        ]);
    }
}
