<?php

namespace App\Repositories\Eloquent;

use App\Models\Routine;
use App\Repositories\Contracts\RoutineRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class RoutineRepository extends BaseRepository implements RoutineRepositoryInterface
{
    public function __construct(Routine $model)
    {
        parent::__construct($model);
    }

    public function getForUser(int $userId): Collection
    {
        return $this->model->where('user_id', $userId)
            ->with(['steps.product'])
            ->get();
    }

    public function findWithSteps(int $routineId): ?Routine
    {
        return $this->model->where('routine_id', $routineId)
            ->with(['steps.product'])
            ->first();
    }
}
