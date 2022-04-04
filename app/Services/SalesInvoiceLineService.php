<?php

namespace App\Services;

use Logging;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceLine;
use Spatie\QueryBuilder\QueryBuilder;

class SalesInvoiceLineService
{
    public function list($relation = null)
    {
        return \Querying::for(SalesInvoiceLine::class)
            ->setFilter(request()->get('filter'))
            ->setSortable(request()->get('sort'))
            ->setSelectables(request()->get('select'))
            ->setSearch(request()->get('search'))
            ->defaultSort('-id')
            ->getQuery();
    }

    public function show($id)
    {
        return QueryBuilder::for(SalesInvoiceLine::where('id', $id))
            ->allowedFields(SalesInvoiceLine::$fields)
            ->allowedIncludes(SalesInvoiceLine::$scopes);
    }

    public function create(array $data, $queryOnly = true)
    {
        $inputAttributes = filterArrayByKeys($data, SalesInvoiceLine::$fields);
        $salesInvoiceLine = SalesInvoiceLine::create($inputAttributes);
        Logging::information('Create Sales Invoice Line', $salesInvoiceLine, 1, 1, $salesInvoiceLine->salesInvoice->tenant_id, 'invoice', $salesInvoiceLine->salesInvoice->id);
        return $this->getOne(['id' => $salesInvoiceLine->id], $queryOnly);
    }

    public function update(array $data, SalesInvoiceLine $salesInvoiceLine)
    {
        $inputAttributes = filterArrayByKeys($data, SalesInvoiceLine::$fields);
        $log['old_values'] = $salesInvoiceLine->getRawDBData();
        $salesInvoiceLine->update($inputAttributes);
        $log['new_values'] = $salesInvoiceLine->getRawDBData();
        $log['changes'] = $salesInvoiceLine->getChanges();
        Logging::information('Update Sales Invoice Line', $log, 1, 1, $salesInvoiceLine->salesInvoice->tenant_id, 'invoice', $salesInvoiceLine->salesInvoice->id);
        return $this->list();
    }

    public function delete(SalesInvoiceLine $salesInvoiceLine)
    {
        Logging::information('Delete Sales Invoice Line', $salesInvoiceLine, 1, 1, $salesInvoiceLine->salesInvoice->tenant_id, 'invoice', $salesInvoiceLine->salesInvoice->id);
        $salesInvoiceLine->delete();
        return $this->list();
    }

    public function count()
    {
        return 0;
    }

    public function getOne($where = [], $queryOnly = true)
    {
        $query = QueryBuilder::for(SalesInvoiceLine::where($where))
            ->allowedIncludes(SalesInvoiceLine::$scopes);
        return $queryOnly ? $query : $query->first();
    }
}
