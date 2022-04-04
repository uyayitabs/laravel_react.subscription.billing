<?php

namespace App\Filters;

use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class PersonFilters implements Filter
{
    public function __invoke(Builder $query, $value, string $property) : Builder
    {
        $query->where(function ($query) use ($value) {
            $query->orWhere('first_name', 'LIKE', '%' . $value . '%');
            $query->orWhere('middle_name', 'LIKE', '%' . $value . '%');
            $query->orWhere('last_name', 'LIKE', '%' . $value . '%');
            $query->orWhere('email', 'LIKE', '%' . $value . '%');
            $query->orWhere('phone', 'LIKE', '%' . $value . '%');
            $query->orWhere('mobile', 'LIKE', '%' . $value . '%');
            $query->orWhere('gender', 'LIKE', '%' . $value . '%');

            $query->orWhereHas('type', function (Builder $query) use ($value) {
                $query->where('type', 'LIKE', '%' . $value . '%');
            });
        });

        return $query;
    }
}
