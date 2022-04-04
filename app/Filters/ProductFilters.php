<?php

namespace App\Filters;

use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class ProductFilters implements Filter
{
    public function __invoke(Builder $query, $value, string $property) : Builder
    {
        $query->where(function ($query) use ($value) {
            $query->orWhere('description', 'LIKE', '%' . $value . '%');
            $query->orWhere('description_long', 'LIKE', '%' . $value . '%');
            $query->orWhere('vendor', 'LIKE', '%' . $value . '%');
            $query->orWhere('vendor_partcode', 'LIKE', '%' . $value . '%');
        });

        return $query;
    }
}
