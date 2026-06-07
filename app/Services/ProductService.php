<?php

namespace App\Services;

use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Models\Product;

class ProductService
{
    protected ProductRepositoryInterface $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function listProducts(array $filters): \Illuminate\Database\Eloquent\Collection
    {
        return $this->productRepository->search($filters);
    }

    public function createProduct(array $data): Product
    {
        /** @var Product $product */
        $product = $this->productRepository->create($data);
        return $product;
    }

    public function updateProduct(int $id, array $data): Product
    {
        $this->productRepository->update($id, $data);
        return $this->productRepository->find($id);
    }

    public function deleteProduct(int $id): bool
    {
        return $this->productRepository->delete($id);
    }
}
