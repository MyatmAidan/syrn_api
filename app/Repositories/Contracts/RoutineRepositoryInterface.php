<?php

namespace App\Repositories\Contracts;

use App\Models\Routine;
use Illuminate\Database\Eloquent\Collection;

interface RoutineRepositoryInterface extends BaseRepositoryInterface
{
    public function getForUser(int $userId): Collection;

    public function findWithSteps(int $routineId): ?Routine;
}
