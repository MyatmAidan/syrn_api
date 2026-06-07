<?php

namespace App\Http\Controllers\Api\Admin\Concerns;

use App\Support\ProductImageHelper;
use Illuminate\Http\Request;

trait HandlesProductImages
{
    protected function applyProductImagesFromRequest(Request $request, array $data): array
    {
        $existing = $data['images'] ?? [];
        if (!is_array($existing)) {
            $existing = [];
        }

        $data['images'] = ProductImageHelper::mergeFromRequest($request, $existing);

        return $data;
    }
}
