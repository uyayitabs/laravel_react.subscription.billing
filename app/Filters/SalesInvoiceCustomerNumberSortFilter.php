<?php

namespace App\Filters;

use Spatie\QueryBuilder\Sorts\Sort;
use Illuminate\Database\Eloquent\Builder;

class SalesInvoiceCustomerNumberSortFilter implements Sort
{
    public function __invoke(Builder $query, bool $descending, string $property)
    {
        $direction = $descending ? 'DESC' : 'ASC';

        $query->leftJoin('relations', 'sales_invoices.relation_id', '=', 'relations.id')
              ->orderByRaw("relations.customer_number {$direction}");
    }
}
