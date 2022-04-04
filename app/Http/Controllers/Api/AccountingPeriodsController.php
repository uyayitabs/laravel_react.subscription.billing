<?php

namespace App\Http\Controllers\Api;

use App\Models\AccountingPeriod;
use App\Services\AccountingPeriodService;
use App\Http\Requests\AccountingPeriodApiRequest;

class AccountingPeriodsController extends BaseController
{
    protected $service;

    public function __construct()
    {
        $this->service = new AccountingPeriodService();
    }

    /**
     * Get tenant-specific accounting periods
     *
     * @return \Illuminate\Http\Response
     */
    public function my($tenantId, $fiscalYearId)
    {
        return $this->sendPaginate(
            $this->service->list(request(), $tenantId, $fiscalYearId),
            'Accounting Periods retrieved successfully.'
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
            $this->service->list(request(), currentTenant('id'), null),
            'Accounting Periods retrieved successfully.'
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(AccountingPeriodApiRequest $request)
    {
        $datas = jsonRecode($request->all(AccountingPeriod::$fields));
        return $this->sendPaginate(
            $this->service->create($datas),
            'Accounting Period saved successfully.'
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->sendSingleResult(
            $this->service->show($id),
            'Accounting Period retrieved successfully.'
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\AccountingPeriod $accountingPeriod
     * @return \Illuminate\Http\Response
     */
    public function update(AccountingPeriod $accountingPeriod, AccountingPeriodApiRequest $request)
    {
        $datas = jsonRecode($request->all(AccountingPeriod::$fields));
        $query = $this->service->update($datas, $accountingPeriod);
        return $this->sendPaginate(
            $query,
            'Accounting Period updated successfully.'
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AccountingPeriod $accountingPeriod
     * @return \Illuminate\Http\Response
     */
    public function destroy(AccountingPeriod $accountingPeriod)
    {
        return $this->sendPaginate(
            $this->service->delete($accountingPeriod),
            'Accounting Period deleted successfully.'
        );
    }
}
