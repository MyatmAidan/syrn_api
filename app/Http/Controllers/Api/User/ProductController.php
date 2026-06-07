<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Display a listing of products (public).
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['category_id', 'brand', 'skin_type', 'skin_type_id', 'skin_concern']);
        $products = $this->productService->listProducts($filters);

        return response()->json([
            'success' => true,
            'data' => ProductResource::collection($products)
        ]);
    }

    /**
     * Display the specified product (public).
     */
    public function show(Product $product): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new ProductResource($product->load(['category', 'brand', 'skinType', 'reviews']))
        ]);
    }
}
