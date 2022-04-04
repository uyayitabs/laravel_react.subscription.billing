<?php

namespace App\Repositories;

use Spatie\QueryBuilder\QueryBuilder;
use App\Models\Country;
use Illuminate\Http\Request;

class CountryRepository extends Repository
{
    // Constructor to bind model to repo
    public function __construct()
    {
        $this->model = Country::class;
    }

    // Get all instances of model
    /**
     * @return Query
     */
    public function all(Request $request)
    {
        $query = \Querying::for(Country::class)
            ->setFilter(request()->get('filter'))
            ->setSortable(request()->get('sort'))
            ->setSelectables(request()->get('select'))
            ->setSearch(request()->get('search'))
            ->defaultSort('id')
            ->getQuery();
//        $query = QueryBuilder::for($this->model, $request)
//            ->allowedIncludes($this->model::$scopes)
//            ->allowedFields($this->model::$fields)
//            ->defaultSort('-id')
//            ->allowedSorts($this->model::$fields);

        return $query;
    }

     /**
     * Get company record counts
     *
     * @return \Illuminate\Http\Response
     */
    public function count()
    {
        return $this->model::count();
    }
}
