<?php

namespace App\Filters;

use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class SalesInvoiceFilters implements Filter
{
    public function __invoke(Builder $query, $value, string $property) : Builder
    {
        $query->where(function ($query) use ($value) {
            $query->orWhere('invoice_no', 'LIKE', '%' . $value . '%');
            $query->orWhere('description', 'LIKE', '%' . $value . '%');
            // $query->orWhere('date', $value);

            $query->orWhereHas('relation', function (Builder $query) use ($value) {
                $query->where('customer_number', 'LIKE', '%' . $value . '%');
                $query->orWhere('company_name', 'LIKE', '%' . $value . '%');

                $query->whereHas('primaryPerson', function (Builder $query) use ($value) {
                    $query->orWhere('first_name', 'LIKE', '%' . $value . '%');
                    $query->orWhere('middle_name', 'LIKE', '%' . $value . '%');
                    $query->orWhere('last_name', 'LIKE', '%' . $value . '%');
                });
            });

            $query->orWhereHas('shippingAddress', function (Builder $query) use ($value) {
                $query->where('street1', 'LIKE', '%' . $value . '%');
                $query->orWhere('street2', 'LIKE', '%' . $value . '%');
                $query->orWhere('zipcode', 'LIKE', '%' . $value . '%');
            });
        });

        return $query;
    }
}
