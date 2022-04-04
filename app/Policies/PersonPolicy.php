<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Person;
use Illuminate\Auth\Access\HandlesAuthorization;

class PersonPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any people.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the person.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Person  $person
     * @return mixed
     */
    public function view(User $user, Person $person)
    {
        return currentTenant('id') == $person->relation->tenant->id;
    }

    /**
     * Determine whether the user can create people.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the person.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Person  $person
     * @return mixed
     */
    public function update(User $user, Person $person)
    {
        return currentTenant('id') == $person->relationsPerson->relation->tenant_id;
    }

    /**
     * Determine whether the user can delete the person.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Person  $person
     * @return mixed
     */
    public function delete(User $user, Person $person)
    {
        return currentTenant('id') == $person->relation->tenant->id;
    }

    /**
     * Determine whether the user can restore the person.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Person  $person
     * @return mixed
     */
    public function restore(User $user, Person $person)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the person.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Person  $person
     * @return mixed
     */
    public function forceDelete(User $user, Person $person)
    {
        //
    }
}
