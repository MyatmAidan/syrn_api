<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\SkinTypeResource;
use App\Models\SkinType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SkinTypeController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => SkinTypeResource::collection(SkinType::orderBy('name')->get()),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:skin_types,name',
            'description' => 'nullable|string|max:255',
        ]);

        $skinType = SkinType::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Skin type created.',
            'data' => new SkinTypeResource($skinType),
        ], 201);
    }

    public function show(SkinType $skinType): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new SkinTypeResource($skinType),
        ]);
    }

    public function update(Request $request, SkinType $skinType): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:50|unique:skin_types,name,' . $skinType->skin_type_id . ',skin_type_id',
            'description' => 'nullable|string|max:255',
        ]);

        $skinType->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Skin type updated.',
            'data' => new SkinTypeResource($skinType),
        ]);
    }

    public function destroy(SkinType $skinType): JsonResponse
    {
        $skinType->delete();

        return response()->json([
            'success' => true,
            'message' => 'Skin type deleted.',
        ]);
    }
}
