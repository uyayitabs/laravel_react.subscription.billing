<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Address;
use App\Models\Relation;
use Illuminate\Auth\Access\HandlesAuthorization;

class AddressPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any addresses.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the address.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Address  $address
     * @return mixed
     */
    public function view(User $user, Address $address)
    {
        return currentTenant('id') == $address->relation->tenant->id;
    }

    /**
     * Determine whether the user can create addresses.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user, Relation $relation)
    {
        return currentTenant('id') == $relation->tenant->id;
    }

    /**
     * Determine whether the user can update the address.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Address  $address
     * @return mixed
     */
    public function update(User $user, Address $address)
    {
        return currentTenant('id') == $address->relation->tenant->id;
    }

    /**
     * Determine whether the user can delete the address.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Address  $address
     * @return mixed
     */
    public function delete(User $user, Address $address)
    {
        return currentTenant('id') == $address->relation->tenant->id;
    }

    /**
     * Determine whether the user can restore the address.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Address  $address
     * @return mixed
     */
    public function restore(User $user, Address $address)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the address.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Address  $address
     * @return mixed
     */
    public function forceDelete(User $user, Address $address)
    {
        //
    }
}
