<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\RecommendationResource;
use App\Models\Recommendation;
use Illuminate\Http\JsonResponse;

class RecommendationController extends Controller
{
    public function index(): JsonResponse
    {
        $recommendations = Recommendation::with(['user', 'product.brand', 'product.category'])
            ->orderByDesc('recommendation_date')
            ->get();

        return response()->json([
            'success' => true,
            'data' => RecommendationResource::collection($recommendations),
        ]);
    }

    public function show(Recommendation $recommendation): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new RecommendationResource(
                $recommendation->load(['user', 'product.brand', 'product.category'])
            ),
        ]);
    }
}
