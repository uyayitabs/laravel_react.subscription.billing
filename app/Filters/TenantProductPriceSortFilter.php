<?php

namespace App\Filters;

use Spatie\QueryBuilder\Sorts\Sort;
use Illuminate\Database\Eloquent\Builder;

class TenantProductPriceSortFilter implements Sort
{
    public function __invoke(Builder $query, bool $descending, string $property)
    {
        $direction = $descending ? 'DESC' : 'ASC';

        $query->leftJoin('tenant_products', 'tenant_products.product_id', '=', 'products.id')
            ->where('tenant_products.tenant_id', currentTenant('id'))
            ->orderByRaw("tenant_products.price {$direction}");
    }
}
