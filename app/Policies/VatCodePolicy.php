<?php

namespace App\Policies;

use App\Models\Tenant;
use App\Models\User;
use App\Models\VatCode;
use Illuminate\Auth\Access\HandlesAuthorization;

class VatCodePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any vat codes.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the vat code.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\VatCode  $vatCode
     * @return mixed
     */
    public function view(User $user, VatCode $vatCode)
    {
        return currentTenant('id') == $vatCode->tenant->id;
    }

    /**
     * Determine whether the user can create vat codes.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user, Tenant $tenant)
    {
        return currentTenant('id') == $tenant->tenant->id;
    }

    /**
     * Determine whether the user can update the vat code.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\VatCode  $vatCode
     * @return mixed
     */
    public function update(User $user, VatCode $vatCode)
    {
        return currentTenant('id') == $vatCode->tenant->id;
    }

    /**
     * Determine whether the user can delete the vat code.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\VatCode  $vatCode
     * @return mixed
     */
    public function delete(User $user, VatCode $vatCode)
    {
        return currentTenant('id') == $vatCode->tenant->id;
    }

    /**
     * Determine whether the user can restore the vat code.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\VatCode  $vatCode
     * @return mixed
     */
    public function restore(User $user, VatCode $vatCode)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the vat code.
     *
     * @param  \App\Models\User  $user
     * @param  \App\VatCode  $vatCode
     * @return mixed
     */
    public function forceDelete(User $user, VatCode $vatCode)
    {
        //
    }
}
