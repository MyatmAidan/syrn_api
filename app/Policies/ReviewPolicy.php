<?php

namespace App\Policies;

use App\Models\Review;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Auth\Authenticatable;
use App\Models\User;

class ReviewPolicy
{
    /**
     * Determine whether the user can update the review.
     */
    public function update(Authenticatable $user, Review $review): Response
    {
        return $user instanceof User && $user->user_id === $review->user_id
            ? Response::allow()
            : Response::deny('You can only modify reviews you wrote.');
    }

    /**
     * Determine whether the user can delete the review.
     */
    public function delete(Authenticatable $user, Review $review): Response
    {
        return $user instanceof User && $user->user_id === $review->user_id
            ? Response::allow()
            : Response::deny('You can only delete reviews you wrote.');
    }
}
