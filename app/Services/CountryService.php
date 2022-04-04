<?php

namespace App\Services;

use App\Models\Country;
use Logging;
use App\Models\State;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class CountryService
{
    public function list(Request $request)
    {
        return QueryBuilder::for(Country::class, $request)
            ->allowedIncludes(Country::$scopes)
            ->defaultSort('name')
            ->allowedSorts(Country::$fields);
    }

    public function optionList()
    {
        return Country::select('id', 'name')->orderBy('name');
    }

    public function create(array $data)
    {
        $country = Country::create($data);
        Logging::information('Create Country', $data, 1, 1);

        return QueryBuilder::for(Country::where('id', $country->id))
            ->allowedIncludes(Country::$scopes)
            ->allowedFields(Country::$fields);
    }

    public function show($id)
    {
        return QueryBuilder::for(Country::where('id', $id))
            ->allowedIncludes(Country::$scopes);
    }

    public function update(array $data, Country $country)
    {
        $log['old_values'] = $country->getRawDBData();

        $country->update($data);
        $log['new_values'] = $country->getRawDBData();
        $log['changes'] = $country->getChanges();

        Logging::information('Update Country', $log, 1, 1);

        return QueryBuilder::for(Country::where('id', $country->id))
            ->allowedIncludes(Country::$scopes);
    }

    public function states(Country $country)
    {
        return State::select('id', 'name')->where('country_id', $country->id)->orderBy('name');
    }
}
