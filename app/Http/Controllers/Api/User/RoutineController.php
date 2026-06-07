<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\CreateRoutineRequest;
use App\Http\Requests\User\UpdateRoutineStepsRequest;
use App\Http\Resources\Api\RoutineResource;
use App\Models\Routine;
use App\Repositories\Contracts\RoutineRepositoryInterface;
use App\Services\RoutineService;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class RoutineController extends Controller
{
    use AuthorizesRequests;

    protected RoutineRepositoryInterface $routineRepository;
    protected RoutineService $routineService;

    public function __construct(
        RoutineRepositoryInterface $routineRepository,
        RoutineService $routineService
    ) {
        $this->routineRepository = $routineRepository;
        $this->routineService = $routineService;
    }

    public function index(): JsonResponse
    {
        $userId = auth()->user()->user_id;
        $routines = $this->routineRepository->getForUser($userId);

        return response()->json([
            'success' => true,
            'data' => RoutineResource::collection($routines)
        ]);
    }

    public function store(CreateRoutineRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['user_id'] = auth()->user()->user_id;

        $routine = $this->routineService->createRoutine($data);

        return response()->json([
            'success' => true,
            'message' => 'Routine created successfully.',
            'data' => new RoutineResource($routine)
        ], 201);
    }

    public function show(Routine $routine): JsonResponse
    {
        $this->authorize('view', $routine);

        $routineLoaded = $this->routineRepository->findWithSteps($routine->routine_id);

        return response()->json([
            'success' => true,
            'data' => new RoutineResource($routineLoaded)
        ]);
    }

    public function updateSteps(UpdateRoutineStepsRequest $request, Routine $routine): JsonResponse
    {
        $this->authorize('update', $routine);

        $updatedRoutine = $this->routineService->updateSteps($routine->routine_id, $request->input('steps'));

        return response()->json([
            'success' => true,
            'message' => 'Routine steps updated successfully.',
            'data' => new RoutineResource($updatedRoutine)
        ]);
    }

    public function destroy(Routine $routine): JsonResponse
    {
        $this->authorize('delete', $routine);

        $this->routineRepository->delete($routine->routine_id);

        return response()->json([
            'success' => true,
            'message' => 'Routine deleted successfully.'
        ]);
    }
}
