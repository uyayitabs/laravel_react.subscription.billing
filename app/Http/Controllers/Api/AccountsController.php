<?php

namespace App\Http\Controllers\Api;

use App\Models\Account;
use App\Models\Tenant;
use App\Services\AccountService;
use App\Http\Requests\AccountApiRequest;

class AccountsController extends BaseController
{
    protected $service;

    public function __construct()
    {
        $this->service = new AccountService();
    }

    /**
     * Get tenant-specific accounts
     *
     * @return \Illuminate\Http\Response
     */
    public function my($tenantId)
    {
        $tenant =  Tenant::find($tenantId);
        $this->authorize('view', $tenant);
        return $this->sendPaginate(
            $this->service->list(request(), $tenantId),
            'Accounts retrieved successfully.'
        );
    }

    /**
     * Return the list accounts with id and description
     * specific for the current tenant_id
     *
     * @return \Illuminate\Http\Response
     */
    public function list($tenantId)
    {
        $tenant =  Tenant::find($tenantId);
        $this->authorize('view', $tenant);
        return $this->sendResults(
            $this->service->optionList(request(), $tenantId),
            'Accounts retrieved successfully.'
        );
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->sendPaginate(
            $this->service->list(request(), currentTenant('id')),
            'Accounts retrieved successfully.'
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(AccountApiRequest $request)
    {
        $datas = jsonRecode($request->all(Account::$fields));
        return $this->sendPaginate(
            $this->service->create($datas),
            'Account saved successfully.'
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Account $account)
    {
        return $this->sendSingleResult(
            $this->service->show($account->id),
            'Account retrieved successfully.'
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\Account $account
     * @return \Illuminate\Http\Response
     */
    public function update(Account $account, AccountApiRequest $request)
    {
        $datas = jsonRecode($request->all(Account::$fields));
        return $this->sendPaginate(
            $this->service->update($datas, $account),
            'Accounts updated successfully.'
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Account $account
     * @return \Illuminate\Http\Response
     */
    public function destroy(Account $account)
    {
        return $this->sendPaginate(
            $this->service->delete($account),
            'Account deleted successfully.'
        );
    }
}
