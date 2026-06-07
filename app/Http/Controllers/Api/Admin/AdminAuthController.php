<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AdminLoginRequest;
use App\Http\Requests\Auth\AdminRegisterRequest;
use App\Http\Resources\Api\AdminResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

class AdminAuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(AdminRegisterRequest $request): JsonResponse
    {
        $result = $this->authService->registerAdmin($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Admin registered successfully.',
            'data' => [
                'admin' => new AdminResource($result['admin']),
                'token' => $result['token'],
            ]
        ], 201);
    }

    public function login(AdminLoginRequest $request): JsonResponse
    {
        $result = $this->authService->loginAdmin(
            $request->input('email'),
            $request->input('password')
        );

        return response()->json([
            'success' => true,
            'message' => 'Admin login successful.',
            'data' => [
                'admin' => new AdminResource($result['admin']),
                'token' => $result['token'],
            ]
        ]);
    }

    public function logout(): JsonResponse
    {
        auth()->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Admin logged out successfully.'
        ]);
    }
}
