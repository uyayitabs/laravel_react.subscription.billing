<?php

namespace App\Repositories;

use Spatie\QueryBuilder\QueryBuilder;
use App\Models\SalesInvoice;

class SalesInvoiceRepository extends Repository
{
    // Constructor to bind model to repo
    public function __construct()
    {
        $this->model = SalesInvoice::class;
    }
    // Get all instances of model
    /**
     * @return Query
     */
    public function all()
    {
        $query = QueryBuilder::for($this->model)
            ->allowedIncludes($this->model::$scopes)
            ->allowedFields($this->model::$fields)
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
