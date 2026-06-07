<?php

namespace App\Repositories\Eloquent;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    public function search(array $filters): Collection
    {
        $query = $this->model->newQuery();

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['brand_id'])) {
            $query->where('brand_id', $filters['brand_id']);
        }

        if (!empty($filters['brand'])) {
            $query->whereHas('brand', function ($q) use ($filters) {
                $q->where('brand_name', 'LIKE', '%' . $filters['brand'] . '%');
            });
        }

        if (!empty($filters['skin_type_id'])) {
            $query->where('skin_type_id', $filters['skin_type_id']);
        }

        if (!empty($filters['skin_type'])) {
            $query->whereHas('skinType', function ($q) use ($filters) {
                $q->where('name', 'LIKE', '%' . $filters['skin_type'] . '%');
            });
        }

        if (!empty($filters['skin_concern'])) {
            $query->where('skin_concern', 'LIKE', '%' . $filters['skin_concern'] . '%');
        }

        return $query->with(['category', 'brand', 'skinType'])->get();
    }
}
