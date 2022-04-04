<?php

namespace App\Filters;

use Spatie\QueryBuilder\Sorts\Sort;
use Illuminate\Database\Eloquent\Builder;

class RelationPrimaryPersonSortFilter implements Sort
{
    public function __invoke(Builder $query, bool $descending, string $property)
    {
        $direction = $descending ? 'DESC' : 'ASC';

        $query->leftJoin('relations_persons', 'relations.id', '=', 'relations_persons.relation_id')
              ->leftJoin('persons', 'relations_persons.person_id', '=', 'persons.id')
              ->orderByRaw("persons.first_name {$direction}, persons.initials {$direction}");
    }
}
