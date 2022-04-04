<?php

namespace App\Services;

use App\Models\FiscalYear;
use Logging;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class FiscalYearService
{
    /**
     * Get tenant-specific fiscal years
     *
     */
    public function list(Request $request, $tenantId)
    {
        if (is_null($tenantId)) {
            $tenantId = currentTenant('id');
        }

        $query = FiscalYear::where('tenant_id', $tenantId);
        $query = QueryBuilder::for($query, $request)
            ->allowedFields(FiscalYear::$fields)
            ->allowedIncludes(FiscalYear::$includes)
            ->defaultSort('-id')
            ->allowedSorts(FiscalYear::$sorts);

        if (request()->has('filter') && isset(request()->filter['keyword'])) {
            $query->search(request()->filter['keyword']);
        }

        return $query;
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     */
    public function show($id)
    {
        return QueryBuilder::for(FiscalYear::where("id", $id))
            ->allowedFields(FiscalYear::$fields)
            ->allowedIncludes(FiscalYear::$includes);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     */
    public function create(array $data)
    {
        FiscalYear::create($data);
        Logging::information('Create Fiscal Year', $data, 1, 1);
        return $this->list(request(), currentTenant('id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\FiscalYear $fiscalYear
     */
    public function update(array $data, FiscalYear $fiscalYear)
    {
        if (is_null($data['tenant_id'])) {
            $data['tenant_id'] = currentTenant('id');
        }

        if (!is_null($fiscalYear)) {
            $log['old_values'] = $fiscalYear->getRawDBData();

            $fiscalYear->update($data);
            $log['new_values'] = $fiscalYear->getRawDBData();
            $log['changes'] = $fiscalYear->getChanges();

            Logging::information('Update Fiscal Year', $log, 1, 1);
        }
        return $this->list(request(), currentTenant('id'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\FiscalYear $fiscalYear
     */
    public function delete(FiscalYear $fiscalYear)
    {
        $fiscalYear->delete();
        return $this->list(request(), currentTenant('id'));
    }

    /**
     * Get fiscal years count
     *
     * @return \Illuminate\Http\Response
     */
    public function count()
    {
        return FiscalYear::count();
    }
}
