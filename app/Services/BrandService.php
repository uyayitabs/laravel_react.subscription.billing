<?php

namespace App\Services;

use App\Models\Brand;
use Logging;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class BrandService
{
    public function list(Request $request)
    {
        return QueryBuilder::for(Brand::class, $request)
            ->allowedFields(Brand::$fields)
            ->allowedIncludes(Brand::$scopes)
            ->allowedFilters(Brand::$fields)
            ->defaultSort('-id')
            ->allowedSorts(Brand::$fields);
    }

    public function create(array $data)
    {
        $brand = Brand::create($data);
        Logging::information('Create Brand', $data, 1, 1);

        return QueryBuilder::for(Brand::where('id', $brand->id))
            ->allowedFields(Brand::$fields)
            ->allowedIncludes(Brand::$scopes);
    }

    public function show($id)
    {
        return QueryBuilder::for(Brand::where('tenant_id', $id))
            ->allowedFields(Brand::$fields)
            ->allowedIncludes(Brand::$scopes);
    }

    public function update(array $data, Brand $brand)
    {
        $log['old_values'] = $brand->getRawDBData();

        $brand->update($data);
        $log['new_values'] = $brand->getRawDBData();
        $log['changes'] = $brand->getChanges();

        Logging::information('Update Brand', $log, 1, 1);

        return QueryBuilder::for(Brand::where('id', $brand->id))
            ->allowedFields(Brand::$fields)
            ->allowedIncludes(Brand::$scopes);
    }
}
