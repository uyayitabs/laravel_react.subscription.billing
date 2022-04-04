<?php

namespace App\Services;

use Logging;
use App\Models\ProductType;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class ProductTypeService
{
    /**
     * Return a paginated list of ProductType
     *
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        $query = QueryBuilder::for(ProductType::class, request())
            ->allowedFields(ProductType::$fields)
            ->allowedFilters(ProductType::$fields)
            ->defaultSort('-id')
            ->allowedSorts(ProductType::$fields);
        return $query;
    }

    /**
     * Store a newly created ProductType
     *
     * @return \Illuminate\Http\Response
     */
    public function create(array $data)
    {
        $productType = ProductType::create($data);

        Logging::information('Create productType', $data, 1, 1);

        return QueryBuilder::for(ProductType::where('id', $productType->id))
            ->allowedFields(ProductType::$fields);
    }

    /**
     * Return ProductType
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return QueryBuilder::for(ProductType::where('id', $id))
            ->allowedFields(ProductType::$fields);
    }

    /**
     * Update the specified product type
     *
     * @param \App\Models\ProductType $productType
     *
     * @return \Illuminate\Http\Response
     */
    public function update(array $data, ProductType $productType)
    {
        $log['old_values'] = $productType->getRawDBData();

        $productType->update($data);
        $log['new_values'] = $productType->getRawDBData();
        $log['changes'] = $productType->getChanges();

        Logging::information('Update productType', $log, 1, 1);

        return QueryBuilder::for(ProductType::where('id', $productType->id))
            ->allowedFields(ProductType::$fields);
    }

    /**
     * Return the list ProductType with id and name
     *
     * @return \Illuminate\Http\Response
     */
    public function optionList()
    {
        return ProductType::select('id', 'type as name');
    }
}
