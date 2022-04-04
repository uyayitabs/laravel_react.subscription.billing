<?php

namespace App\Services;

use Logging;
use App\Models\VatCode;
use App\Models\Warehouse;
use Spatie\QueryBuilder\QueryBuilder;

class VatCodeService
{
    /**
     * Return the list products with id and name
     */
    public function list($tenantId)
    {
        if (is_null($tenantId)) {
            $tenantId = currentTenant('id');
        }

        $query = QueryBuilder::for(VatCode::where('tenant_id', $tenantId))
            ->allowedFields(VatCode::$fields)
            ->allowedIncludes(VatCode::$includes)
            ->defaultSort('-id')
            ->allowedSorts(VatCode::$fields);

        if (request()->has('filter') && isset(request()->filter['keyword'])) {
            $query->search(request()->filter['keyword']);
        }

        return $query;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\VatCode  $vatCode
     */
    public function show($id)
    {
        return QueryBuilder::for(VatCode::where('id', $id))
            ->allowedIncludes(['tenant']);
    }

    public function listEntries($vatCode)
    {
        return QueryBuilder::for(VatCode::where('journal_id', $vatCode->id))
            ->allowedFields(VatCode::$fields)
            ->allowedIncludes(VatCode::$includes)
            ->defaultSort('-id')
            ->allowedSorts(VatCode::$sorts);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Models\Tenant  $tenant
     */
    public function create(array $datas)
    {
        $vatCode = VatCode::create($datas);
        Logging::information('Create Vat Code', $datas, 1, 1);

        return QueryBuilder::for(VatCode::where('id', $vatCode->id))
            ->allowedIncludes(['tenant']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\VatCode  $vatCode
     */
    public function update(VatCode $vatCode, array $datas)
    {
        $log = [];
        $log['old_values'] = $vatCode->getRawDBData();

        $vatCode->update($datas);
        $log['new_values'] = $vatCode->getRawDBData();
        $log['changes'] = $vatCode->getChanges();

        Logging::information('Update Vat Code', $log, 1, 1);

        return QueryBuilder::for(Warehouse::where('id', $vatCode->id))
            ->allowedIncludes(['tenant']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\VatCode  $vatCode
     */
    public function delete(VatCode $vatCode)
    {
        $vatCode->delete();
        return $this->list(request(), currentTenant('id'));
    }

    public function count()
    {
        return VatCode::count();
    }
}
