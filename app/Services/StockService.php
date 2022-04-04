<?php

namespace App\Services;

use Logging;
use App\Models\Stock;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class StockService
{
    /**
     * Return a paginated list of stocks
     *
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        return QueryBuilder::for(Stock::class, request())
            ->allowedIncludes(Stock::$scopes)
            ->allowedFields(Stock::$fields)
            ->allowedFilters(Stock::$fields)
            ->defaultSort('-id')
            ->allowedSorts(Stock::$fields);
    }

    /**
     * Store a newly created stock
     *
     * @return \Illuminate\Http\Response
     */
    public function create(array $data)
    {
        $stock = Stock::create($data);
        Logging::information('Create Stock', $data, 1, 1);

        return QueryBuilder::for(Stock::where('id', $stock->id))
            ->allowedIncludes(Stock::$scopes)
            ->allowedFields(Stock::$fields);
    }

    /**
     * Return the specified stock
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return QueryBuilder::for(Stock::where('id', $id))
            ->allowedIncludes(Stock::$scopes)
            ->allowedFields(Stock::$fields);
    }

    /**
     * Update the specified stock
     *
     * @param \App\Models\Stock $stock
     *
     * @return \Illuminate\Http\Response
     */
    public function update(array $data, Stock $stock)
    {
        $log['old_values'] = $stock->getRawDBData();
        $stock->update($data);

        $log['new_values'] = $stock->getRawDBData();
        $log['changes'] = $stock->getChanges();

        Logging::information('Update Stock', $log, 1, 1);

        return QueryBuilder::for(Stock::where('id', $stock->id))
            ->allowedIncludes(Stock::$scopes)
            ->allowedFields(Stock::$fields);
    }
}
