<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\FavouriteResource;
use App\Models\Favourite;
use Illuminate\Http\JsonResponse;

class FavouriteController extends Controller
{
    public function index(): JsonResponse
    {
        $favourites = Favourite::with(['user', 'product.brand', 'product.category'])
            ->orderByDesc('saved_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => FavouriteResource::collection($favourites),
        ]);
    }

    public function show(Favourite $favourite): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new FavouriteResource(
                $favourite->load(['user', 'product.brand', 'product.category'])
            ),
        ]);
    }
}
