<?php

namespace App\Policies;

use App\Models\User;
use App\Models\SalesInvoice;
use Illuminate\Auth\Access\HandlesAuthorization;

class SalesInvoicePolicy extends BasePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any sales invoices.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the sales invoice.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SalesInvoice  $salesInvoice
     * @return mixed
     */
    public function view(User $user, SalesInvoice $salesInvoice)
    {
        $permitted = $salesInvoice->tenant ? $this->permitted(
            $user,
            $salesInvoice->tenant,
            request()->header('socketid')
        ) : false;
        return $permitted;
    }

    /**
     * Determine whether the user can create sales invoices.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the sales invoice.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SalesInvoice  $salesInvoice
     * @return mixed
     */
    public function update(User $user, SalesInvoice $salesInvoice)
    {
        return currentTenant('id') == $salesInvoice->tenant->id;
    }

    /**
     * Determine whether the user can delete the sales invoice.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SalesInvoice  $salesInvoice
     * @return mixed
     */
    public function delete(User $user, SalesInvoice $salesInvoice)
    {
        return currentTenant('id') == $salesInvoice->tenant->id;
    }

    /**
     * Determine whether the user can restore the sales invoice.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SalesInvoice  $salesInvoice
     * @return mixed
     */
    public function restore(User $user, SalesInvoice $salesInvoice)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the sales invoice.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SalesInvoice  $salesInvoice
     * @return mixed
     */
    public function forceDelete(User $user, SalesInvoice $salesInvoice)
    {
        //
    }
}
