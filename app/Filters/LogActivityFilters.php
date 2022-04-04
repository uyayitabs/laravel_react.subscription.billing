<?php

namespace App\Filters;

use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class LogActivityFilters implements Filter
{
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        $query->where(function ($query) use ($value) {
            $query->orWhere('message', 'LIKE', '%' . $value . '%');
            $query->orWhere('json_data', 'LIKE', '%' . $value . '%');
            $query->orWhere('severity', 'LIKE', '%' . $value . '%');
            $query->orWhere('username', 'LIKE', '%' . $value . '%');
        });

        return $query;
    }
}
