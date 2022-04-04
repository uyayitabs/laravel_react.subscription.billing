<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Tenant;
use Illuminate\Auth\Access\HandlesAuthorization;

class TenantPolicy extends BasePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any tenants.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the tenant.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Tenant  $tenant
     * @return mixed
     */
    public function view(User $user, Tenant $tenant)
    {
        return $this->permitted($user, $tenant);
    }

    /**
     * Determine whether the user can create tenants.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the tenant.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Tenant  $tenant
     * @return mixed
     */
    public function update(User $user, Tenant $tenant)
    {
        return currentTenant('id') == $tenant->id;
    }

    /**
     * Determine whether the user can delete the tenant.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Tenant  $tenant
     * @return mixed
     */
    public function delete(User $user, Tenant $tenant)
    {
        return currentTenant('id') == $tenant->id;
    }

    /**
     * Determine whether the user can restore the tenant.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Tenant  $tenant
     * @return mixed
     */
    public function restore(User $user, Tenant $tenant)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the tenant.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Tenant  $tenant
     * @return mixed
     */
    public function forceDelete(User $user, Tenant $tenant)
    {
        //
    }
}
