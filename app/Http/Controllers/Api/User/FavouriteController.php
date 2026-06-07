<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\FavouriteResource;
use App\Models\Favourite;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class FavouriteController extends Controller
{
    use AuthorizesRequests;

    public function index(): JsonResponse
    {
        $userId = auth()->user()->user_id;
        $favourites = Favourite::where('user_id', $userId)->with('product')->get();

        return response()->json([
            'success' => true,
            'data' => FavouriteResource::collection($favourites)
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,product_id'
        ]);

        $userId = auth()->user()->user_id;
        $productId = $request->input('product_id');

        $favourite = Favourite::firstOrCreate([
            'user_id' => $userId,
            'product_id' => $productId
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product saved to favourites.',
            'data' => new FavouriteResource($favourite->load('product'))
        ], 201);
    }

    public function destroy(Favourite $favourite): JsonResponse
    {
        $this->authorize('delete', $favourite);

        $favourite->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product removed from favourites.'
        ]);
    }
}
