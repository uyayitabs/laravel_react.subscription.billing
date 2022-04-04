<?php

namespace App\Policies;

use App\Models\User;
use App\Models\SubscriptionLine;
use App\Models\SubscriptionLinePrice;
use Illuminate\Auth\Access\HandlesAuthorization;

class SubscriptionLinePricePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any subscription line prices.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the subscription line price.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SubscriptionLinePrice  $subscriptionLinePrice
     * @return mixed
     */
    public function view(User $user, SubscriptionLinePrice $subscriptionLinePrice)
    {
        return currentTenant('id') == $subscriptionLinePrice->subscriptionLine->subscription->relation->tenant->id;
    }

    /**
     * Determine whether the user can create subscription line prices.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user, SubscriptionLine $subscriptionLine)
    {
        return currentTenant('id') == $subscriptionLine->subscription->relation->tenant->id;
    }

    /**
     * Determine whether the user can update the subscription line price.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SubscriptionLinePrice  $subscriptionLinePrice
     * @return mixed
     */
    public function update(User $user, SubscriptionLinePrice $subscriptionLinePrice)
    {
        return currentTenant('id') == $subscriptionLinePrice->subscriptionLine->subscription->relation->tenant->id;
    }

    /**
     * Determine whether the user can delete the subscription line price.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SubscriptionLinePrice  $subscriptionLinePrice
     * @return mixed
     */
    public function delete(User $user, SubscriptionLinePrice $subscriptionLinePrice)
    {
        return currentTenant('id') == $subscriptionLinePrice->subscriptionLine->subscription->relation->tenant->id;
    }

    /**
     * Determine whether the user can restore the subscription line price.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SubscriptionLinePrice  $subscriptionLinePrice
     * @return mixed
     */
    public function restore(User $user, SubscriptionLinePrice $subscriptionLinePrice)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the subscription line price.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SubscriptionLinePrice  $subscriptionLinePrice
     * @return mixed
     */
    public function forceDelete(User $user, SubscriptionLinePrice $subscriptionLinePrice)
    {
        //
    }
}
