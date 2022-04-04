<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Tenant;
use App\Models\Relation;
use Illuminate\Auth\Access\HandlesAuthorization;

class RelationPolicy extends BasePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any relations.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return false;
    }

    /**
     * Determine whether the user can view the relation.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Relation  $relation
     * @return mixed
     */
    public function view(User $user, Relation $relation)
    {
        $permitted = $relation->tenant ? $this->permitted(
            $user,
            $relation->tenant,
            request()->header('socketid')
        ) : false;
        return $permitted;
    }

    /**
     * Determine whether the user can create relations.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user, Tenant $tenant)
    {
        return currentTenant('id') == $tenant->id;
    }

    /**
     * Determine whether the user can update the relation.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Relation  $relation
     * @return mixed
     */
    public function update(User $user, Relation $relation)
    {
        return currentTenant('id') == $relation->tenant_id;
    }

    /**
     * Determine whether the user can delete the relation.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Relation  $relation
     * @return mixed
     */
    public function delete(User $user, Relation $relation)
    {
        return currentTenant('id') == $relation->tenant_id;
    }

    /**
     * Determine whether the user can restore the relation.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Relation  $relation
     * @return mixed
     */
    public function restore(User $user, Relation $relation)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the relation.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Relation  $relation
     * @return mixed
     */
    public function forceDelete(User $user, Relation $relation)
    {
        return false;
    }
}
