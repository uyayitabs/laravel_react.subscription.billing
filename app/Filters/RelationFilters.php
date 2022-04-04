<?php

namespace App\Filters;

use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class RelationFilters implements Filter
{
    public function __invoke(Builder $query, $value, string $property) : Builder
    {
        if (is_array($value)) {
            foreach ($value as $keyword) {
                $keyword = trim($keyword);
                $query->where(function ($query) use ($keyword) {
                    $query->orWhere('company_name', 'LIKE', '%' . $keyword . '%');
                    $query->orWhere('customer_number', 'LIKE', '%' . $keyword . '%');
                    $query->orWhere('email', 'LIKE', '%' . $keyword . '%');
                    $query->orWhere('phone', 'LIKE', '%' . $keyword . '%');
                    $query->orWhere('bank_account', 'LIKE', '%' . $keyword . '%');
        
                    $query->orWhereHas('addresses', function (Builder $query) use ($keyword) {
                        $query->where('street1', 'LIKE', '%' . $keyword . '%');
                        $query->orWhere('house_number', 'LIKE', '%' . $keyword . '%');
                        $query->orWhere('street2', 'LIKE', '%' . $keyword . '%');
                        $query->orWhere('zipcode', 'LIKE', '%' . $keyword . '%');
        
                        $query->orWhereHas('city', function (Builder $query) use ($keyword) {
                            $query->where('name', 'LIKE', '%' . $keyword . '%');
                            $query->orWhere('municipality', 'LIKE', '%' . $keyword . '%');
                        });
                    });
        
                    $query->orWhereHas('persons', function (Builder $query) use ($keyword) {
                        $query->where('email', 'LIKE', '%' . $keyword . '%');
                        $query->orWhere('first_name', 'LIKE', '%' . $keyword . '%');
                        $query->orWhere('middle_name', 'LIKE', '%' . $keyword . '%');
                        $query->orWhere('last_name', 'LIKE', '%' . $keyword . '%');
                    });
                });
            }
        } else {
            $value = trim($value);
            $query->where(function ($query) use ($value) {
                $query->orWhere('company_name', 'LIKE', '%' . $value . '%');
                $query->orWhere('customer_number', 'LIKE', '%' . $value . '%');
                $query->orWhere('email', 'LIKE', '%' . $value . '%');
                // $query->orWhere('phone', 'LIKE', '%' . $value . '%');
                $query->orWhere('bank_account', 'LIKE', '%' . $value . '%');
    
                $query->orWhereHas('addresses', function (Builder $query) use ($value) {
                    $query->where('street1', 'LIKE', '%' . $value . '%');
                    $query->orWhere('house_number', 'LIKE', '%' . $value . '%');
                    $query->orWhere('street2', 'LIKE', '%' . $value . '%');
                    $query->orWhere('zipcode', 'LIKE', '%' . $value . '%');
    
                    $query->orWhereHas('city', function (Builder $query) use ($value) {
                        $query->where('name', 'LIKE', '%' . $value . '%');
                        $query->orWhere('municipality', 'LIKE', '%' . $value . '%');
                    });
                });
    
                $query->orWhereHas('persons', function (Builder $query) use ($value) {
                    $query->where('email', 'LIKE', '%' . $value . '%');
                    $query->orWhere('first_name', 'LIKE', '%' . $value . '%');
                    $query->orWhere('middle_name', 'LIKE', '%' . $value . '%');
                    $query->orWhere('last_name', 'LIKE', '%' . $value . '%');
                });
            });
        }

        return $query;
    }
}
