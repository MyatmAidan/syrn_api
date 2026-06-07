<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class CartService
{
    public function getCartForUser(int $userId): Cart
    {
        return Cart::firstOrCreate(['user_id' => $userId]);
    }

    public function getCartWithItems(int $userId): Cart
    {
        $cart = $this->getCartForUser($userId);

        return $cart->load([
            'items.product.category',
            'items.product.brand',
            'items.product.skinType',
        ]);
    }

    public function addItem(int $userId, int $productId, int $quantity): Cart
    {
        if ($quantity < 1) {
            throw new UnprocessableEntityHttpException('Quantity must be at least 1.');
        }

        $product = Product::findOrFail($productId);

        if ($product->qty < $quantity) {
            throw new UnprocessableEntityHttpException('Insufficient stock for this product.');
        }

        $cart = $this->getCartForUser($userId);

        $item = CartItem::where('cart_id', $cart->cart_id)
            ->where('product_id', $productId)
            ->first();

        if ($item) {
            $newQty = $item->quantity + $quantity;
            if ($product->qty < $newQty) {
                throw new UnprocessableEntityHttpException('Insufficient stock for this product.');
            }
            $item->update(['quantity' => $newQty]);
        } else {
            CartItem::create([
                'cart_id' => $cart->cart_id,
                'product_id' => $productId,
                'quantity' => $quantity,
            ]);
        }

        return $this->getCartWithItems($userId);
    }

    public function updateItemQuantity(int $userId, int $cartItemId, int $quantity): Cart
    {
        if ($quantity < 1) {
            throw new UnprocessableEntityHttpException('Quantity must be at least 1.');
        }

        $cart = $this->getCartForUser($userId);
        $item = CartItem::where('cart_id', $cart->cart_id)
            ->where('cart_item_id', $cartItemId)
            ->firstOrFail();

        $product = Product::findOrFail($item->product_id);
        if ($product->qty < $quantity) {
            throw new UnprocessableEntityHttpException('Insufficient stock for this product.');
        }

        $item->update(['quantity' => $quantity]);

        return $this->getCartWithItems($userId);
    }

    public function removeItem(int $userId, int $cartItemId): Cart
    {
        $cart = $this->getCartForUser($userId);

        CartItem::where('cart_id', $cart->cart_id)
            ->where('cart_item_id', $cartItemId)
            ->delete();

        return $this->getCartWithItems($userId);
    }

    public function clearCart(int $cartId): void
    {
        CartItem::where('cart_id', $cartId)->delete();
    }
}
