<?php

namespace App\Filters;

use \Spatie\QueryBuilder\Sorts\Sort;
use Illuminate\Database\Eloquent\Builder;

class PlanLineProductSortFilters implements Sort
{
    public function __invoke(Builder $query, bool $descending, string $property)
    {
        $direction = $descending ? 'DESC' : 'ASC';

        $query->leftJoin('products', 'plan_lines.product_id', '=', 'products.id')
            ->orderByRaw("products.description {$direction}");
    }
}
