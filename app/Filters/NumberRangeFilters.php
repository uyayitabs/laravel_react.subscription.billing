<?php

namespace App\Filters;

use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class NumberRangeFilters implements Filter
{
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        $query->where(function ($query) use ($value) {
            $query->orWhere('type', 'LIKE', '%' . $value . '%');
            $query->orWhere('description', 'LIKE', '%' . $value . '%');
            $query->orWhere('start', 'LIKE', '%' . $value . '%');
            $query->orWhere('end', 'LIKE', '%' . $value . '%');
            $query->orWhere('format', 'LIKE', '%' . $value . '%');
        });

        return $query;
    }
}
