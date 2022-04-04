<?php

namespace App\Services;

use Logging;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class WarehouseService
{
    /**
     * Return a listing warehouses
     *
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        return QueryBuilder::for(Warehouse::class, request())
            ->allowedIncludes(Warehouse::$scopes)
            ->allowedFields(Warehouse::$fields)
            ->allowedFilters(Warehouse::$fields)
            ->defaultSort('-id')
            ->allowedSorts(Warehouse::$fields);
    }

    /**
     * Store a newly created warehouse
     *
     * @return \Illuminate\Http\Response
     */
    public function create(array $data)
    {
        $warehouse = Warehouse::create($data);
        Logging::information('Create Warehouse', $data, 1, 1);

        return QueryBuilder::for(Warehouse::where('id', $warehouse->id))
            ->allowedIncludes(Warehouse::$scopes)
            ->allowedFields(Warehouse::$fields);
    }

    /**
     * Return the specified warehouse
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return QueryBuilder::for(Warehouse::where('id', $id))
            ->allowedIncludes(Warehouse::$scopes)
            ->allowedFields(Warehouse::$fields);
    }

    /**
     * Update the specified warehouse
     *
     * @param \App\Models\Warehouse $warehouse
     *
     * @return \Illuminate\Http\Response
     */
    public function update(array $data, Warehouse $warehouse)
    {
        $log['old_values'] = $warehouse->getRawDBData();

        $warehouse->update($data);
        $log['new_values'] = $warehouse->getRawDBData();
        $log['changes'] = $warehouse->getChanges();

        Logging::information('Update Warehouse', $log, 1, 1);

        return QueryBuilder::for(Warehouse::where('id', $warehouse->id))
            ->allowedIncludes(Warehouse::$scopes)
            ->allowedFields(Warehouse::$fields);
    }
}
