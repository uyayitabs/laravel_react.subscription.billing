<?php

namespace App\Filters;

use \Spatie\QueryBuilder\Sorts\Sort;
use Illuminate\Database\Eloquent\Builder;

class PlanLineLineTypeSortFilters implements Sort
{
    public function __invoke(Builder $query, bool $descending, string $property)
    {
        $direction = $descending ? 'DESC' : 'ASC';

        $query->leftJoin('plan_subscription_line_types', 'plan_lines.plan_line_type', '=', 'plan_subscription_line_types.id')
            ->orderByRaw("plan_subscription_line_types.line_type {$direction}");
    }
}
