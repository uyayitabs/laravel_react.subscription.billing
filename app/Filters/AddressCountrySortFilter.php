<?php

namespace App\Filters;

use Spatie\QueryBuilder\Sorts\Sort;
use Illuminate\Database\Eloquent\Builder;

class AddressCountrySortFilter implements Sort
{
    public function __invoke(Builder $query, bool $descending, string $property)
    {
        $direction = $descending ? 'DESC' : 'ASC';

        $query->leftJoin('countries', 'addresses.country_id', '=', 'countries.id')
            ->orderByRaw("countries.name {$direction}");
    }
}
