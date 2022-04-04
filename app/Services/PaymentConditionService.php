<?php

namespace App\Services;

use Logging;
use App\Http\Resources\BaseResourceCollection;
use App\Http\Resources\PaymentConditionResource;
use App\Models\PaymentCondition;
use App\Models\Tenant;
use Illuminate\Support\Str;

class PaymentConditionService
{
    protected $paymentCondition;

    public function list(Tenant $tenant)
    {
        $query = $tenant->paymentConditions();

        // search filter
        $query = $this->handleSearchFilters(
            $query,
            request()->query("filter", [])
        );

        // sorting
        $query = $this->handleSorting(
            $query,
            request()->query('sort', '-created_at')
        );

        // pagination (page & limit)
        $limit = request()->query('offset', 10);
        $query = $query->paginate($limit);

        // JsonResource implementation
        $query->transform(function (PaymentCondition $paymentCondition) {
            return (new PaymentConditionResource(
                $paymentCondition,
                'Payment condition retrieved successfully.',
                true,
                true
            ));
        });

        return new BaseResourceCollection($query);
    }

    public function handleSearchFilters($modelQuery, $searchFilter)
    {
        if (array_key_exists('keyword', $searchFilter)) {
            $value = $searchFilter['keyword'];
            $modelQuery->search($value);
        }
        return $modelQuery;
    }

    public function handleSorting($modelQuery, $sortFilter)
    {
        if ($sortFilter) {
            $sequence = Str::contains($sortFilter, '-') ? 'DESC' : 'ASC';
            $columnName = str_replace('-', '', $sortFilter);
            $modelQuery->orderBy($columnName, $sequence);
        }
        return $modelQuery;
    }

    public function create(Tenant $tenant)
    {
        $attributes = request(PaymentCondition::$fields);
        $attributes['created_by'] = request()->user()->id;

        $paymentCondition = $tenant->paymentConditions()->create($attributes);

        if ($paymentCondition && $paymentCondition->default) {
            $paymentCondition->tenant->paymentConditions()
                ->where('default', 1)
                ->whereNotIn('id', [$paymentCondition->id])
                ->update(['default' => 0]);
        }
        return $paymentCondition;
    }

    public function update(PaymentCondition $paymentCondition)
    {
        $attributes = request(PaymentCondition::$fields);
        $attributes['updated_by'] = request()->user()->id;

        $log['old_values'] = $paymentCondition->getRawDBData();

        $paymentCondition->update($attributes);

        $log['new_values'] = $paymentCondition->getRawDBData();
        $log['changes'] = $paymentCondition->getChanges();

        Logging::information('Update Payment Conditions', $log, 1, 1);

        if ($paymentCondition && $paymentCondition->default) {
            $paymentCondition->tenant->paymentConditions()
                ->where('default', 1)
                ->whereNotIn('id', [$paymentCondition->id])
                ->update(['default' => 0]);
        }
        return $paymentCondition;
    }
}
