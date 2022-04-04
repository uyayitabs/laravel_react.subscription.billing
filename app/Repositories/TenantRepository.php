<?php

namespace App\Repositories;

use Spatie\QueryBuilder\QueryBuilder;
use App\Models\Tenant;
use Illuminate\Http\Request;

class TenantRepository extends Repository
{
    // Constructor to bind model to repo
    public function __construct()
    {
        $this->model = Tenant::class;
    }

    // Get all instances of model
    /**
     * @return Query
     */
    public function all(Request $request)
    {
        $query = QueryBuilder::for($this->model, $request)
            ->allowedFields($this->model::$fields)
            ->allowedIncludes($this->model::$scopes)
            ->defaultSort('-id')
            ->allowedSorts($this->model::$fields);

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
