<?php

namespace App\Services;

use App\Repositories\Contracts\RoutineRepositoryInterface;
use App\Models\Routine;
use App\Models\RoutineStep;
use Illuminate\Support\Facades\DB;

class RoutineService
{
    protected RoutineRepositoryInterface $routineRepository;

    public function __construct(RoutineRepositoryInterface $routineRepository)
    {
        $this->routineRepository = $routineRepository;
    }

    public function createRoutine(array $data): Routine
    {
        return DB::transaction(function () use ($data) {
            /** @var Routine $routine */
            $routine = $this->routineRepository->create([
                'user_id' => $data['user_id'],
                'routine_name' => $data['routine_name'],
                'routine_time' => $data['routine_time'],
            ]);

            if (!empty($data['steps'])) {
                foreach ($data['steps'] as $index => $step) {
                    RoutineStep::create([
                        'routine_id' => $routine->routine_id,
                        'product_id' => $step['product_id'],
                        'step_order' => $step['step_order'] ?? ($index + 1),
                        'instruction' => $step['instruction'] ?? null,
                    ]);
                }
            }

            return $routine->load('steps.product');
        });
    }

    public function updateSteps(int $routineId, array $stepsData): Routine
    {
        return DB::transaction(function () use ($routineId, $stepsData) {
            // Remove existing steps first
            RoutineStep::where('routine_id', $routineId)->delete();

            // Re-create steps
            foreach ($stepsData as $index => $step) {
                RoutineStep::create([
                    'routine_id' => $routineId,
                    'product_id' => $step['product_id'],
                    'step_order' => $step['step_order'] ?? ($index + 1),
                    'instruction' => $step['instruction'] ?? null,
                ]);
            }

            return $this->routineRepository->findWithSteps($routineId);
        });
    }
}
