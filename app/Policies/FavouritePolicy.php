<?php

namespace App\Policies;

use App\Models\Favourite;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Auth\Authenticatable;
use App\Models\User;

class FavouritePolicy
{
    /**
     * Determine whether the user can view the favourite.
     */
    public function view(Authenticatable $user, Favourite $favourite): Response
    {
        return $user instanceof User && $user->user_id === $favourite->user_id
            ? Response::allow()
            : Response::deny('You do not own this favourite item.');
    }

    /**
     * Determine whether the user can delete the favourite.
     */
    public function delete(Authenticatable $user, Favourite $favourite): Response
    {
        return $user instanceof User && $user->user_id === $favourite->user_id
            ? Response::allow()
            : Response::deny('You do not own this favourite item.');
    }
}
