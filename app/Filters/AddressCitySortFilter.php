<?php

namespace App\Filters;

use \Spatie\QueryBuilder\Sorts\Sort;
use Illuminate\Database\Eloquent\Builder;

class AddressCitySortFilter implements Sort
{
    public function __invoke(Builder $query, bool $descending, string $property)
    {
        $direction = $descending ? 'DESC' : 'ASC';

        $query->leftJoin('cities', 'addresses.city_id', '=', 'cities.id')
            ->orderByRaw("cities.name {$direction}");
    }
}
