<?php

namespace App\Repositories;

use Spatie\QueryBuilder\QueryBuilder;
use App\Models\WebServiceLog;

class WebServiceLogRepository extends Repository
{
    // Constructor to bind model to repo
    public function __construct()
    {
        $this->model = WebServiceLog::class;
    }

    // Get all instances of model
    /**
     * @return Query
     */
    public function all()
    {
        $query = QueryBuilder::for($this->model)
            ->allowedFields($this->model::$fields)
            ->defaultSort('-id')
            ->allowedSorts($this->model::$fields);

        return $query;
    }
}
