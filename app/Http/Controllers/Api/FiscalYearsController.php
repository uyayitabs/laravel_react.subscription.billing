<?php

namespace App\Http\Controllers\Api;

use App\Models\FiscalYear;
use App\Models\Tenant;
use App\Services\FiscalYearService;
use App\Http\Requests\FiscalYearApiRequest;

class FiscalYearsController extends BaseController
{
    protected $service;

    public function __construct()
    {
        $this->service = new FiscalYearService();
    }

    /**
     * Get tenant-specific fiscal years
     *
     * @return \Illuminate\Http\Response
     */
    public function my($tenantId)
    {
        $tenant =  Tenant::find($tenantId);
        $this->authorize('view', $tenant);
        return $this->sendPaginate(
            $this->service->list(request(), $tenantId),
            'Fiscal Years retrieved successfully.'
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
            'Fiscal Years retrieved successfully.'
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(FiscalYearApiRequest $request)
    {
        $datas = jsonRecode($request->all(FiscalYear::$fields));
        return $this->sendPaginate(
            $this->service->create($datas),
            'Fiscal Year retrieved successfully.'
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
            'Fiscal Year retrieved successfully.'
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\FiscalYear $fiscalYear
     * @return \Illuminate\Http\Response
     */
    public function update(FiscalYear $fiscalYear, FiscalYearApiRequest $request)
    {
        $datas = jsonRecode($request->all(FiscalYear::$fields));
        return $this->sendPaginate(
            $this->service->update($datas, $fiscalYear),
            'Fiscal Year updated successfully.'
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\FiscalYear $fiscalYear
     * @return \Illuminate\Http\Response
     */
    public function destroy(FiscalYear $fiscalYear)
    {
        return $this->sendPaginate(
            $this->service->delete($fiscalYear),
            'Fiscal Year deleted successfully.'
        );
    }
}
