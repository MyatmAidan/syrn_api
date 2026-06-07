<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Http\Resources\Api\AdminResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $admins = Admin::all();
        return response()->json([
            'success' => true,
            'data' => AdminResource::collection($admins)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'admin_name' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:admins,email',
            'password' => 'required|string|min:6',
        ]);

        $admin = Admin::create([
            'admin_name' => $validated['admin_name'],
            'email' => $validated['email'],
            'password_hash' => Hash::make($validated['password']),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Admin created successfully.',
            'data' => new AdminResource($admin)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Admin $admin): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new AdminResource($admin)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Admin $admin): JsonResponse
    {
        $validated = $request->validate([
            'admin_name' => 'required|string|max:100',
            'email' => [
                'required',
                'email',
                'max:100',
                Rule::unique('admins', 'email')->ignore($admin->admin_id, 'admin_id'),
            ],
            'password' => 'nullable|string|min:6',
        ]);

        $updateData = [
            'admin_name' => $validated['admin_name'],
            'email' => $validated['email'],
        ];

        if (!empty($validated['password'])) {
            $updateData['password_hash'] = Hash::make($validated['password']);
        }

        $admin->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Admin updated successfully.',
            'data' => new AdminResource($admin)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Admin $admin): JsonResponse
    {
        // Don't let an admin delete themselves if they are the authenticated user
        if (auth()->user()->admin_id === $admin->admin_id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own admin account.'
            ], 400);
        }

        $admin->delete();

        return response()->json([
            'success' => true,
            'message' => 'Admin deleted successfully.'
        ]);
    }
}
