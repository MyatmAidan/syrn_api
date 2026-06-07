<?php

namespace App\Http\Resources\Api;

use App\Support\ProductImageHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'product_id' => $this->product_id,
            'category_id' => $this->category_id,
            'brand_id' => $this->brand_id,
            'admin_id' => $this->admin_id,
            'skin_type_id' => $this->skin_type_id,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'product_name' => $this->product_name,
            'brand' => new BrandResource($this->whenLoaded('brand')),
            'skin_type' => new SkinTypeResource($this->whenLoaded('skinType')),
            'ingredients' => $this->ingredients,
            'skin_concern' => $this->skin_concern,
            'price' => $this->price,
            'qty' => $this->qty,
            'description' => $this->description,
            'images' => $this->images ?? [],
            'image_urls' => ProductImageHelper::resolveUrls($this->images ?? []),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
