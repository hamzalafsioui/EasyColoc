<?php

namespace App\Services;

use App\Models\User;

class ReputationManager
{
    /**
     * Increase the user reputation.
     *
     * @param User $user
     * @param int $points
     * @return void
     */
    public function increase(User $user, int $points = 1): void
    {
        $user->increment('reputation', $points);
    }

    /**
     * Decrease the user reputation.
     *
     * @param User $user
     * @param int $points
     * @return void
     */
    public function decrease(User $user, int $points = 1): void
    {
        if ($user->reputation > 0) {
            $user->decrement('reputation', $points); // prevent negative reputation  min($points, $user->reputation) Not Implemented Yet
        }
    }
}
