<?php

namespace App\Filters;

use Spatie\QueryBuilder\Sorts\Sort;
use Illuminate\Database\Eloquent\Builder;

class TenantProductDescriptionSortFilter implements Sort
{
    public function __invoke(Builder $query, bool $descending, string $property)
    {
        $direction = $descending ? 'DESC' : 'ASC';

        $query->leftJoin('products', 'products.id', '=', 'tenant_products.product_id')
            ->orderByRaw("products.description {$direction}");
    }
}
