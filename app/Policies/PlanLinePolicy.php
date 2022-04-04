<?php

namespace App\Policies;

use App\Models\User;
use App\Models\PlanLine;
use Illuminate\Auth\Access\HandlesAuthorization;

class PlanLinePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any plan lines.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the plan line.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PlanLine  $planLine
     * @return mixed
     */
    public function view(User $user, PlanLine $planLine)
    {
        return currentTenant('id') == $planLine->plan->tenant->id;
    }

    /**
     * Determine whether the user can create plan lines.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the plan line.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PlanLine  $planLine
     * @return mixed
     */
    public function update(User $user, PlanLine $planLine)
    {
        return currentTenant('id') == $planLine->plan->tenant->id;
    }

    /**
     * Determine whether the user can delete the plan line.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PlanLine  $planLine
     * @return mixed
     */
    public function delete(User $user, PlanLine $planLine)
    {
        return currentTenant('id') == $planLine->plan->tenant->id;
    }

    /**
     * Determine whether the user can restore the plan line.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PlanLine  $planLine
     * @return mixed
     */
    public function restore(User $user, PlanLine $planLine)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the plan line.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PlanLine  $planLine
     * @return mixed
     */
    public function forceDelete(User $user, PlanLine $planLine)
    {
        //
    }
}
