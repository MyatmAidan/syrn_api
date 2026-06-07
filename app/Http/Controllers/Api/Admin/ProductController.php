<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\Admin\Concerns\HandlesProductImages;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Http\Resources\Api\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
class ProductController extends Controller
{
    use HandlesProductImages;

    public function __construct(protected ProductService $productService) {}

    public function index(): JsonResponse
    {
        $products = Product::with(['category', 'brand', 'skinType'])->get();

        return response()->json([
            'success' => true,
            'data' => ProductResource::collection($products),
        ]);
    }

    public function show(Product $product): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new ProductResource($product->load(['category', 'brand', 'skinType'])),
        ]);
    }

    public function store(CreateProductRequest $request): JsonResponse
    {
        $data = $this->applyProductImagesFromRequest($request, $request->validated());
        $data['admin_id'] = auth()->user()->admin_id;

        $product = $this->productService->createProduct($data);

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully.',
            'data' => new ProductResource($product->load(['category', 'brand', 'skinType'])),
        ], 201);
    }

    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $data = $this->applyProductImagesFromRequest($request, $request->validated());

        $updatedProduct = $this->productService->updateProduct($product->product_id, $data);

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully.',
            'data' => new ProductResource($updatedProduct->load(['category', 'brand', 'skinType'])),
        ]);
    }

    public function destroy(Product $product): JsonResponse
    {
        $this->productService->deleteProduct($product->product_id);

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully.',
        ]);
    }
}
