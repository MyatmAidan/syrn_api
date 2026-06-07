<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Http\Resources\Api\OrderResource;
use App\Http\Resources\Api\ProductResource;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Carbon\Carbon;

class DashboardService
{
    public function getOverview(int $chartDays = 14): array
    {
        $chartDays = max(7, min($chartDays, 30));
        $startDate = Carbon::now()->subDays($chartDays - 1)->startOfDay();
        $dateKeys = $this->dateRangeKeys($startDate, $chartDays);

        $ordersByStatus = Order::query()
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->map(fn ($count) => (int) $count)
            ->all();

        $ordersPerDay = Order::query()
            ->where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as day, COUNT(*) as count')
            ->groupBy('day')
            ->pluck('count', 'day')
            ->map(fn ($count) => (int) $count)
            ->all();

        $revenuePerDay = Order::query()
            ->where('status', OrderStatus::Confirmed->value)
            ->where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as day, SUM(total) as revenue')
            ->groupBy('day')
            ->pluck('revenue', 'day')
            ->map(fn ($revenue) => round((float) $revenue, 2))
            ->all();

        $ordersOverTime = [];
        $revenueOverTime = [];
        foreach ($dateKeys as $date) {
            $ordersOverTime[] = [
                'date' => $date,
                'label' => Carbon::parse($date)->format('M d'),
                'count' => (int) ($ordersPerDay[$date] ?? 0),
            ];
            $revenueOverTime[] = [
                'date' => $date,
                'label' => Carbon::parse($date)->format('M d'),
                'revenue' => (float) ($revenuePerDay[$date] ?? 0),
            ];
        }

        $statusChart = collect(OrderStatus::cases())->map(fn (OrderStatus $status) => [
            'status' => $status->value,
            'label' => $this->orderStatusLabel($status->value),
            'count' => (int) ($ordersByStatus[$status->value] ?? 0),
        ])->values()->all();

        $topProducts = OrderItem::query()
            ->join('orders', 'order_items.order_id', '=', 'orders.order_id')
            ->where('orders.status', OrderStatus::Confirmed->value)
            ->selectRaw('order_items.product_name, SUM(order_items.quantity) as units_sold, SUM(order_items.line_total) as revenue')
            ->groupBy('order_items.product_name')
            ->orderByDesc('units_sold')
            ->limit(6)
            ->get()
            ->map(fn ($row) => [
                'product_name' => $row->product_name,
                'units_sold' => (int) $row->units_sold,
                'revenue' => round((float) $row->revenue, 2),
            ])
            ->all();

        $ratingDistribution = Review::query()
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->orderBy('rating')
            ->pluck('count', 'rating')
            ->map(fn ($count) => (int) $count)
            ->all();

        $ratingChart = collect(range(1, 5))->map(fn (int $rating) => [
            'rating' => $rating,
            'count' => (int) ($ratingDistribution[$rating] ?? 0),
        ])->all();

        $avgRating = Review::avg('rating');

        $confirmedRevenue = (float) Order::query()
            ->where('status', OrderStatus::Confirmed->value)
            ->sum('total');

        $recentOrders = Order::with(['user'])
            ->orderByDesc('created_at')
            ->limit(6)
            ->get();

        $lowStockProducts = Product::with(['category', 'brand'])
            ->where('qty', '<=', 10)
            ->orderBy('qty')
            ->limit(6)
            ->get();

        return [
            'counts' => [
                'users' => User::count(),
                'products' => Product::count(),
                'orders' => Order::count(),
                'reviews' => Review::count(),
                'categories' => Category::count(),
                'brands' => Brand::count(),
                'low_stock_products' => Product::where('qty', '<=', 10)->count(),
                'orders_pending_payment' => (int) ($ordersByStatus[OrderStatus::PendingPayment->value] ?? 0),
                'orders_awaiting_verification' => (int) ($ordersByStatus[OrderStatus::AwaitingVerification->value] ?? 0),
                'orders_confirmed' => (int) ($ordersByStatus[OrderStatus::Confirmed->value] ?? 0),
                'orders_cancelled' => (int) ($ordersByStatus[OrderStatus::Cancelled->value] ?? 0),
            ],
            'commerce' => [
                'total_revenue' => round($confirmedRevenue, 2),
                'avg_order_value' => $this->avgConfirmedOrderValue(),
                'avg_review_rating' => $avgRating ? round((float) $avgRating, 1) : null,
            ],
            'charts' => [
                'orders_over_time' => $ordersOverTime,
                'revenue_over_time' => $revenueOverTime,
                'orders_by_status' => $statusChart,
                'rating_distribution' => $ratingChart,
                'top_products' => $topProducts,
            ],
            'recent_orders' => $recentOrders
                ->map(fn ($order) => (new OrderResource($order))->resolve())
                ->values()
                ->all(),
            'low_stock_products' => $lowStockProducts
                ->map(fn ($product) => (new ProductResource($product))->resolve())
                ->values()
                ->all(),
        ];
    }

    protected function dateRangeKeys(Carbon $startDate, int $days): array
    {
        $keys = [];
        for ($i = 0; $i < $days; $i++) {
            $keys[] = $startDate->copy()->addDays($i)->toDateString();
        }

        return $keys;
    }

    protected function avgConfirmedOrderValue(): float
    {
        $avg = Order::query()
            ->where('status', OrderStatus::Confirmed->value)
            ->avg('total');

        return $avg ? round((float) $avg, 2) : 0;
    }

    protected function orderStatusLabel(string $status): string
    {
        return match ($status) {
            OrderStatus::PendingPayment->value => 'Pending payment',
            OrderStatus::AwaitingVerification->value => 'Awaiting verification',
            OrderStatus::Confirmed->value => 'Confirmed',
            OrderStatus::Cancelled->value => 'Cancelled',
            default => ucfirst(str_replace('_', ' ', $status)),
        };
    }
}
