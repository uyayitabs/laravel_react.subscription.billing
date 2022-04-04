<?php

namespace App\Policies;

use App\Models\User;
use App\Models\SalesInvoiceLine;
use Illuminate\Auth\Access\HandlesAuthorization;

class SalesInvoiceLinePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any sales invoice lines.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the sales invoice line.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SalesInvoiceLine  $salesInvoiceLine
     * @return mixed
     */
    public function view(User $user, SalesInvoiceLine $salesInvoiceLine)
    {
        //
    }

    /**
     * Determine whether the user can create sales invoice lines.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the sales invoice line.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SalesInvoiceLine  $salesInvoiceLine
     * @return mixed
     */
    public function update(User $user, SalesInvoiceLine $salesInvoiceLine)
    {
        //
    }

    /**
     * Determine whether the user can delete the sales invoice line.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SalesInvoiceLine  $salesInvoiceLine
     * @return mixed
     */
    public function delete(User $user, SalesInvoiceLine $salesInvoiceLine)
    {
        //
    }

    /**
     * Determine whether the user can restore the sales invoice line.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SalesInvoiceLine  $salesInvoiceLine
     * @return mixed
     */
    public function restore(User $user, SalesInvoiceLine $salesInvoiceLine)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the sales invoice line.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SalesInvoiceLine  $salesInvoiceLine
     * @return mixed
     */
    public function forceDelete(User $user, SalesInvoiceLine $salesInvoiceLine)
    {
        //
    }
}
