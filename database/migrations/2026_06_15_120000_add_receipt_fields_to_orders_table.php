<?php

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('receipt_number', 40)->nullable()->unique()->after('order_number');
            $table->timestamp('confirmed_at')->nullable()->after('customer_note');
        });

        Order::query()
            ->where('status', OrderStatus::Confirmed)
            ->whereNull('receipt_number')
            ->with('payment')
            ->each(function (Order $order) {
                $order->update([
                    'receipt_number' => 'RCPT-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6)),
                    'confirmed_at' => $order->payment?->reviewed_at ?? $order->updated_at,
                ]);
            });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['receipt_number', 'confirmed_at']);
        });
    }
};
