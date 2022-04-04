<?php

namespace App\Filters;

use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class PlanFilters implements Filter
{
    public function __invoke(Builder $query, $value, string $property) : Builder
    {
        $query->where(function ($query) use ($value) {
            $query->orWhere('area_code_id', 'LIKE', '%' . $value . '%');
            $query->orWhere('description', 'LIKE', '%' . $value . '%');
            // $query->orWhere('plan_start', $value);
            // $query->orWhere('plan_stop', $value);
        });

        return $query;
    }
}
