<?php

namespace App\Policies;

use App\Models\Perizinan;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PerizinanPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Perizinan $perizinan): bool
    {
        return $user->hasRole('admin') || $perizinan->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Perizinan $perizinan): bool
    {
        return $user->hasRole('admin') || $perizinan->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Perizinan $perizinan): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the status of the model.
     */
    public function updateStatus(User $user, Perizinan $perizinan): bool
    {
        return $user->hasRole('admin');
    }
}
