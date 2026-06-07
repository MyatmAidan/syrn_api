<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\SkinTypeResource;
use App\Models\SkinType;
use Illuminate\Http\JsonResponse;

class SkinTypeController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => SkinTypeResource::collection(SkinType::orderBy('name')->get()),
        ]);
    }
}
