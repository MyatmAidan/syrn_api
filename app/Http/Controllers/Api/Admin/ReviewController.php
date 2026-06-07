<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ReviewResource;
use App\Models\Review;
use Illuminate\Http\JsonResponse;

class ReviewController extends Controller
{
    public function index(): JsonResponse
    {
        $reviews = Review::with(['user', 'product.brand', 'product.category'])
            ->orderByDesc('review_date')
            ->get();

        return response()->json([
            'success' => true,
            'data' => ReviewResource::collection($reviews),
        ]);
    }

    public function show(Review $review): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new ReviewResource(
                $review->load(['user', 'product.brand', 'product.category'])
            ),
        ]);
    }
}
