<?php

namespace App\Filters;

use Spatie\QueryBuilder\Sorts\Sort;
use Illuminate\Database\Eloquent\Builder;

class RelationAddressTypeSortFilter implements Sort
{
    public function __invoke(Builder $query, bool $descending, string $property)
    {
        $direction = $descending ? 'DESC' : 'ASC';

        $query->leftJoin('address_types', 'addresses.address_type_id', '=', 'address_types.id')
            ->orderByRaw("address_types.type {$direction}");
    }
}
