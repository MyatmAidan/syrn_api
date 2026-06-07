<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Routine;
use App\Models\RoutineStep;
use App\Http\Resources\Api\RoutineResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class RoutineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $routines = Routine::with(['user', 'steps.product.brand', 'steps.product.category'])->get();
        return response()->json([
            'success' => true,
            'data' => RoutineResource::collection($routines)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,user_id',
            'routine_name' => 'required|string|max:100',
            'routine_time' => 'required|string|max:100',
            'steps' => 'nullable|array',
            'steps.*.product_id' => 'required|exists:products,product_id',
            'steps.*.step_order' => 'nullable|integer',
            'steps.*.instruction' => 'nullable|string',
        ]);

        $routine = DB::transaction(function () use ($validated) {
            $routine = Routine::create([
                'user_id' => $validated['user_id'],
                'routine_name' => $validated['routine_name'],
                'routine_time' => $validated['routine_time'],
            ]);

            if (!empty($validated['steps'])) {
                foreach ($validated['steps'] as $index => $step) {
                    RoutineStep::create([
                        'routine_id' => $routine->routine_id,
                        'product_id' => $step['product_id'],
                        'step_order' => $step['step_order'] ?? ($index + 1),
                        'instruction' => $step['instruction'] ?? null,
                    ]);
                }
            }

            return $routine;
        });

        return response()->json([
            'success' => true,
            'message' => 'Routine created successfully.',
            'data' => new RoutineResource($routine->load(['user', 'steps.product.brand', 'steps.product.category']))
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Routine $routine): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new RoutineResource($routine->load(['user', 'steps.product.brand', 'steps.product.category']))
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Routine $routine): JsonResponse
    {
        $validated = $request->validate([
            'routine_name' => 'required|string|max:100',
            'routine_time' => 'required|string|max:100',
            'steps' => 'nullable|array',
            'steps.*.product_id' => 'required|exists:products,product_id',
            'steps.*.step_order' => 'nullable|integer',
            'steps.*.instruction' => 'nullable|string',
        ]);

        DB::transaction(function () use ($routine, $validated) {
            $routine->update([
                'routine_name' => $validated['routine_name'],
                'routine_time' => $validated['routine_time'],
            ]);

            if (isset($validated['steps'])) {
                // Delete old steps and recreate
                RoutineStep::where('routine_id', $routine->routine_id)->delete();

                foreach ($validated['steps'] as $index => $step) {
                    RoutineStep::create([
                        'routine_id' => $routine->routine_id,
                        'product_id' => $step['product_id'],
                        'step_order' => $step['step_order'] ?? ($index + 1),
                        'instruction' => $step['instruction'] ?? null,
                    ]);
                }
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Routine updated successfully.',
            'data' => new RoutineResource($routine->load(['user', 'steps.product.brand', 'steps.product.category']))
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Routine $routine): JsonResponse
    {
        DB::transaction(function () use ($routine) {
            RoutineStep::where('routine_id', $routine->routine_id)->delete();
            $routine->delete();
        });

        return response()->json([
            'success' => true,
            'message' => 'Routine deleted successfully.'
        ]);
    }
}
