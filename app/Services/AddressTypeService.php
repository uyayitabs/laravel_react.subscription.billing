<?php

namespace App\Services;

use App\Models\AddressType;
use Logging;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class AddressTypeService
{
    public function list(Request $request)
    {
        return QueryBuilder::for(AddressType::class, request())
            ->allowedIncludes(AddressType::$scopes)
            ->allowedFields(AddressType::$fields)
            ->allowedFilters(AddressType::$fields)
            ->defaultSort('-id')
            ->allowedSorts(AddressType::$fields);
    }

    public function show($id)
    {
        return QueryBuilder::for(AddressType::where('id', $id))
            ->allowedIncludes(AddressType::$scopes)
            ->allowedFields(AddressType::$fields);
    }

    public function create(array $data)
    {
        $addressType = AddressType::create($data);

        Logging::information('Create AddressType', $data, 1, 1);

        return QueryBuilder::for(AddressType::where('id', $addressType->id))
            ->allowedIncludes(AddressType::$scopes)
            ->allowedFields(AddressType::$fields);
    }

    public function update(array $data, AddressType $addressType)
    {
        $log['old_values'] = $addressType->getRawDBData();

        $addressType->update($data);
        $log['new_values'] = $addressType->getRawDBData();
        $log['changes'] = $addressType->getChanges();

        Logging::information('Update AddressType', $log, 1, 1);

        return QueryBuilder::for(AddressType::where('id', $addressType->id))
           ->allowedIncludes(AddressType::$scopes)
           ->allowedFields(AddressType::$fields);
    }
}
