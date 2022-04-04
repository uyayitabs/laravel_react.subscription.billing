<?php

namespace App\Filters;

use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class AddressFilters implements Filter
{
    public function __invoke(Builder $query, $value, string $property) : Builder
    {
        $query->where(function ($query) use ($value) {
            $query->where('street1', 'LIKE', '%' . $value . '%');
            $query->orWhere('street2', 'LIKE', '%' . $value . '%');
            $query->orWhere('zipcode', 'LIKE', '%' . $value . '%');

            $query->orWhere('house_number', 'LIKE', '%' . $value . '%');
            $query->orWhere('house_number_suffix', 'LIKE', '%' . $value . '%');
            $query->orWhere('room', 'LIKE', '%' . $value . '%');
           
            $query->orWhereHas('addressType', function (Builder $query) use ($value) {
                $query->where('type', 'LIKE', '%' . $value . '%');
            });

            //TODO Country
            //TODO City
            //TODO City
        });

        return $query;
    }
}
