<?php

namespace App\Policies;

use App\Models\Routine;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Auth\Authenticatable;
use App\Models\User;

class RoutinePolicy
{
    /**
     * Determine whether the user can view the routine.
     */
    public function view(Authenticatable $user, Routine $routine): Response
    {
        return $user instanceof User && $user->user_id === $routine->user_id
            ? Response::allow()
            : Response::deny('You do not own this skincare routine.');
    }

    /**
     * Determine whether the user can update the routine.
     */
    public function update(Authenticatable $user, Routine $routine): Response
    {
        return $user instanceof User && $user->user_id === $routine->user_id
            ? Response::allow()
            : Response::deny('You do not own this skincare routine.');
    }

    /**
     * Determine whether the user can delete the routine.
     */
    public function delete(Authenticatable $user, Routine $routine): Response
    {
        return $user instanceof User && $user->user_id === $routine->user_id
            ? Response::allow()
            : Response::deny('You do not own this skincare routine.');
    }
}
