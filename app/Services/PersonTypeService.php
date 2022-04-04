<?php

namespace App\Services;

use Logging;
use App\Models\PersonType;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class PersonTypeService
{
    /**
     * Return a paginated list of brands
     *
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        return QueryBuilder::for(PersonType::class, request())
            ->allowedIncludes(PersonType::$scopes)
            ->allowedFields(PersonType::$fields)
            ->allowedFilters(PersonType::$fields)
            ->defaultSort('-id')
            ->allowedSorts(PersonType::$fields);
    }

    /**
     * Store a newly created person type
     *
     * @return \Illuminate\Http\Response
     */
    public function create(array $data)
    {
        $personType = PersonType::create($data);
        Logging::information('Create PersonType', $data, 1, 1);

        return QueryBuilder::for(PersonType::where('id', $personType->id))
            ->allowedIncludes(PersonType::$scopes)
            ->allowedFields(PersonType::$fields);
    }

    /**
     * Return the specified person type
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return QueryBuilder::for(PersonType::where('id', $id))
            ->allowedIncludes(PersonType::$scopes)
            ->allowedFields(PersonType::$fields);
    }

    /**
     * Update the specified person type.
     *
     * @param \App\Models\PersonType $personType
     *
     * @return \Illuminate\Http\Response
     */
    public function update(array $data, PersonType $personType)
    {
        $log['old_values'] = $personType->getRawDBData();

        $personType->update($data);
        $log['new_values'] = $personType->getRawDBData();
        $log['changes'] = $personType->getChanges();

        Logging::information('Update PersonType', $log, 1, 1);

        return QueryBuilder::for(PersonType::where('id', $personType->id))
            ->allowedIncludes(PersonType::$scopes)
            ->allowedFields(PersonType::$fields);
    }

    /**
     * Return the list person types with id and name
     *
     * @return \Illuminate\Http\Response
     */
    public function optionList()
    {
        return PersonType::select('id', 'type as name');
    }
}
