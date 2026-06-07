<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentVerificationStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderPayment;
use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class OrderService
{
    public function __construct(
        protected CartService $cartService
    ) {}

    public function checkout(int $userId, array $shipping): Order
    {
        $cart = $this->cartService->getCartWithItems($userId);

        if ($cart->items->isEmpty()) {
            throw new UnprocessableEntityHttpException('Your cart is empty.');
        }

        return DB::transaction(function () use ($userId, $shipping, $cart) {
            $subtotal = 0;
            $orderItems = [];

            foreach ($cart->items as $item) {
                $product = $item->product;
                if (!$product || $product->price === null) {
                    throw new UnprocessableEntityHttpException('A product in your cart is unavailable.');
                }
                if ($product->qty < $item->quantity) {
                    throw new UnprocessableEntityHttpException(
                        "Insufficient stock for {$product->product_name}."
                    );
                }

                $unitPrice = (float) $product->price;
                $lineTotal = $unitPrice * $item->quantity;
                $subtotal += $lineTotal;

                $orderItems[] = [
                    'product_id' => $product->product_id,
                    'product_name' => $product->product_name,
                    'unit_price' => $unitPrice,
                    'quantity' => $item->quantity,
                    'line_total' => $lineTotal,
                ];
            }

            $order = Order::create([
                'user_id' => $userId,
                'order_number' => $this->generateOrderNumber(),
                'status' => OrderStatus::PendingPayment,
                'subtotal' => $subtotal,
                'total' => $subtotal,
                'shipping_name' => $shipping['shipping_name'],
                'shipping_phone' => $shipping['shipping_phone'],
                'shipping_address' => $shipping['shipping_address'],
                'customer_note' => $shipping['customer_note'] ?? null,
            ]);

            foreach ($orderItems as $row) {
                OrderItem::create(array_merge(['order_id' => $order->order_id], $row));
            }

            $this->cartService->clearCart($cart->cart_id);

            return $order->load(['items.product', 'payment.paymentBank', 'user']);
        });
    }

    public function submitPayment(
        int $userId,
        int $orderId,
        int $paymentBankId,
        UploadedFile $slipImage
    ): Order {
        $order = Order::with('items')->where('user_id', $userId)->findOrFail($orderId);

        if ($order->status !== OrderStatus::PendingPayment) {
            throw new UnprocessableEntityHttpException('This order cannot accept payment in its current status.');
        }

        if ($order->payment) {
            throw new UnprocessableEntityHttpException('Payment has already been submitted for this order.');
        }

        $path = $slipImage->store('payment-slips', 'public');

        OrderPayment::create([
            'order_id' => $order->order_id,
            'payment_bank_id' => $paymentBankId,
            'amount' => $order->total,
            'slip_image' => $path,
            'status' => PaymentVerificationStatus::Pending,
        ]);

        $order->update(['status' => OrderStatus::AwaitingVerification]);

        return $order->fresh(['items.product', 'payment.paymentBank', 'user']);
    }

    public function approvePayment(int $orderId, int $adminId, ?string $adminNote = null): Order
    {
        return DB::transaction(function () use ($orderId, $adminId, $adminNote) {
            $order = Order::with(['items', 'payment'])->findOrFail($orderId);

            if ($order->status !== OrderStatus::AwaitingVerification || !$order->payment) {
                throw new UnprocessableEntityHttpException('Order is not awaiting payment verification.');
            }

            foreach ($order->items as $item) {
                $product = Product::lockForUpdate()->find($item->product_id);
                if (!$product || $product->qty < $item->quantity) {
                    throw new UnprocessableEntityHttpException(
                        "Insufficient stock to confirm order for {$item->product_name}."
                    );
                }
                $product->decrement('qty', $item->quantity);
            }

            $order->payment->update([
                'status' => PaymentVerificationStatus::Approved,
                'reviewed_by_admin_id' => $adminId,
                'admin_note' => $adminNote,
                'reviewed_at' => now(),
            ]);

            $order->update(['status' => OrderStatus::Confirmed]);

            return $order->fresh(['items.product', 'payment.paymentBank', 'user']);
        });
    }

    public function rejectPayment(int $orderId, int $adminId, ?string $adminNote = null): Order
    {
        $order = Order::with('payment')->findOrFail($orderId);

        if ($order->status !== OrderStatus::AwaitingVerification || !$order->payment) {
            throw new UnprocessableEntityHttpException('Order is not awaiting payment verification.');
        }

        $order->payment->update([
            'status' => PaymentVerificationStatus::Rejected,
            'reviewed_by_admin_id' => $adminId,
            'admin_note' => $adminNote,
            'reviewed_at' => now(),
        ]);

        $order->update(['status' => OrderStatus::Cancelled]);

        return $order->fresh(['items.product', 'payment.paymentBank', 'user']);
    }

    protected function generateOrderNumber(): string
    {
        do {
            $number = 'SYRN-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
        } while (Order::where('order_number', $number)->exists());

        return $number;
    }
}
