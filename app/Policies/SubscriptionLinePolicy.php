<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Subscription;
use App\Models\SubscriptionLine;
use Illuminate\Auth\Access\HandlesAuthorization;

class SubscriptionLinePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any subscription lines.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the subscription line.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SubscriptionLine  $subscriptionLine
     * @return mixed
     */
    public function view(User $user, SubscriptionLine $subscriptionLine)
    {
        return currentTenant('id') == $subscriptionLine->subscription->relation->tenant->id;
    }

    /**
     * Determine whether the user can create subscription lines.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Subscription  $subscription
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can create subscription lines.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Subscription  $subscription
     * @return mixed
     */
    public function createSubscriptionLinePrice(User $user, SubscriptionLine $subscriptionLine)
    {
        return currentTenant('id') == $subscriptionLine->subscription->relation->tenant->id;
    }

    /**
     * Determine whether the user can update the subscription line.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SubscriptionLine  $subscriptionLine
     * @return mixed
     */
    public function update(User $user, SubscriptionLine $subscriptionLine)
    {
        return currentTenant('id') == $subscriptionLine->subscription->relation->tenant->id;
    }

    /**
     * Determine whether the user can delete the subscription line.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SubscriptionLine  $subscriptionLine
     * @return mixed
     */
    public function delete(User $user, SubscriptionLine $subscriptionLine)
    {
        return currentTenant('id') == $subscriptionLine->subscription->relation->tenant->id;
    }

    /**
     * Determine whether the user can restore the subscription line.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SubscriptionLine  $subscriptionLine
     * @return mixed
     */
    public function restore(User $user, SubscriptionLine $subscriptionLine)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the subscription line.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SubscriptionLine  $subscriptionLine
     * @return mixed
     */
    public function forceDelete(User $user, SubscriptionLine $subscriptionLine)
    {
        //
    }
}
