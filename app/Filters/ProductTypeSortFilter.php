<?php

namespace App\Filters;

use Spatie\QueryBuilder\Sorts\Sort;
use Illuminate\Database\Eloquent\Builder;

class ProductTypeSortFilter implements Sort
{
    public function __invoke(Builder $query, bool $descending, string $property)
    {
        // $direction = $descending ? 'DESC' : 'ASC';

        // $query->leftJoin('product_types', 'product_types.id', '=', 'products.product_type_id')
        //     ->orderByRaw("product_types.type {$direction}");
    }
}
