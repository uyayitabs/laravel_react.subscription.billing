<?php

namespace App\Policies;

use App\Models\User;
use App\Models\PlanLinePrice;
use Illuminate\Auth\Access\HandlesAuthorization;

class PlanLinePricePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any plan line prices.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the plan line price.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PlanLinePrice  $planLinePrice
     * @return mixed
     */
    public function view(User $user, PlanLinePrice $planLinePrice)
    {
        return currentTenant('id') == $planLinePrice->planLine->plan->tenant->id;
    }

    /**
     * Determine whether the user can create plan line prices.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the plan line price.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PlanLinePrice  $planLinePrice
     * @return mixed
     */
    public function update(User $user, PlanLinePrice $planLinePrice)
    {
        return currentTenant('id') == $planLinePrice->planLine->plan->tenant->id;
    }

    /**
     * Determine whether the user can delete the plan line price.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PlanLinePrice  $planLinePrice
     * @return mixed
     */
    public function delete(User $user, PlanLinePrice $planLinePrice)
    {
        return currentTenant('id') == $planLinePrice->planLine->plan->tenant->id;
    }

    /**
     * Determine whether the user can restore the plan line price.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PlanLinePrice  $planLinePrice
     * @return mixed
     */
    public function restore(User $user, PlanLinePrice $planLinePrice)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the plan line price.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PlanLinePrice  $planLinePrice
     * @return mixed
     */
    public function forceDelete(User $user, PlanLinePrice $planLinePrice)
    {
        //
    }
}
