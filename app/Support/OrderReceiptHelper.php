<?php

namespace App\Support;

use App\Enums\OrderStatus;
use App\Models\Order;

class OrderReceiptHelper
{
    public static function isAvailable(Order $order): bool
    {
        $status = $order->status?->value ?? $order->status;

        return $status === OrderStatus::Confirmed->value
            && $order->payment
            && ($order->payment->status?->value ?? $order->payment->status) === 'approved';
    }

    public static function toArray(Order $order): ?array
    {
        if (!self::isAvailable($order)) {
            return null;
        }

        $order->loadMissing(['items', 'payment.paymentBank', 'payment.reviewedBy', 'user']);

        $payment = $order->payment;
        $issuedAt = $order->confirmed_at ?? $payment?->reviewed_at ?? $order->updated_at;

        return [
            'receipt_number' => $order->receipt_number ?? ('RCPT-' . $order->order_number),
            'order_number' => $order->order_number,
            'issued_at' => $issuedAt?->toIso8601String(),
            'customer' => [
                'name' => $order->shipping_name,
                'email' => $order->user?->email,
                'phone' => $order->shipping_phone,
            ],
            'shipping' => [
                'name' => $order->shipping_name,
                'phone' => $order->shipping_phone,
                'address' => $order->shipping_address,
            ],
            'items' => $order->items->map(fn ($item) => [
                'product_name' => $item->product_name,
                'quantity' => $item->quantity,
                'unit_price' => (string) $item->unit_price,
                'line_total' => (string) $item->line_total,
            ])->values()->all(),
            'subtotal' => (string) $order->subtotal,
            'total' => (string) $order->total,
            'customer_note' => $order->customer_note,
            'payment' => [
                'bank_name' => $payment?->paymentBank?->bank_name,
                'account_name' => $payment?->paymentBank?->account_name,
                'account_number' => $payment?->paymentBank?->account_number,
                'amount' => (string) ($payment?->amount ?? $order->total),
                'verified_at' => $payment?->reviewed_at?->toIso8601String(),
                'verified_by' => $payment?->reviewedBy?->admin_name,
                'admin_note' => $payment?->admin_note,
            ],
        ];
    }
}
