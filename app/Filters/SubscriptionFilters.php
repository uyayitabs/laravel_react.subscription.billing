<?php

namespace App\Filters;

use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class SubscriptionFilters implements Filter
{
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        $query->where(function ($query) use ($value) {
            $query->orWhere('id', 'LIKE', '%' . $value . '%');
            $query->orWhere('description', 'LIKE', '%' . $value . '%');
            $query->orWhere('description_long', 'LIKE', '%' . $value . '%');
            // $query->orWhere('subscription_start', $value);
            // $query->orWhere('subscription_stop', $value);

            $query->orWhereHas('plan', function (Builder $query) use ($value) {
                $query->where('description', 'LIKE', '%' . $value . '%');
                $query->orWhere('description_long', 'LIKE', '%' . $value . '%');
            });

            $query->orWhereHas('relation', function (Builder $query) use ($value) {
                $query->where('customer_number', 'LIKE', '%' . $value . '%');
            });
        });

        return $query;
    }
}
