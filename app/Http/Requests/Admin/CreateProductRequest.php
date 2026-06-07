<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CreateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => 'required|integer|exists:categories,category_id',
            'product_name' => 'required|string|max:150',
            'brand_id' => 'nullable|integer|exists:brands,brand_id',
            'ingredients' => 'nullable|string',
            'skin_type_id' => 'nullable|integer|exists:skin_types,skin_type_id',
            'skin_concern' => 'nullable|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'qty' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'existing_images' => 'nullable',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,jpg,png,webp,gif|max:5120',
        ];
    }
}
