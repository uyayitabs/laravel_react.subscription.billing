<?php

namespace App\Services;

use Logging;
use App\Models\PlanLinePrice;
use App\Models\PlanLine;
use App\Http\Resources\BaseResourceCollection;
use App\Http\Resources\PlanLinePriceResource;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\QueryBuilder;

class PlanLinePriceService extends BaseService
{
    public function linePrices(PlanLine $planLine)
    {
        $query = $planLine->planLinePrices();

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
        $query->transform(function (PlanLinePrice $planLinePrice) {
            return (new PlanLinePriceResource(
                $planLinePrice,
                'Plan line price retrieved successfully.',
                true,
                true
            ));
        });

        return new BaseResourceCollection($query);
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

    /**
     * Return a list of plan line prices
     *
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        return QueryBuilder::for(PlanLinePrice::class, request())
            ->allowedFields(PlanLinePrice::$fields)
            ->allowedIncludes(PlanLinePrice::$scopes)
            ->allowedFilters(PlanLinePrice::$fields)
            ->defaultSort('-id')
            ->allowedSorts(PlanLinePrice::$fields);
    }

    public function doSave(array $data, PlanLine $plan_line)
    {
        $attributes = filterArrayByKeys($data, PlanLinePrice::$fields);
        if (isset($attributes['id'])) {
            $planLinePrice = $plan_line->planLinePrices()->find($attributes['id']);
            $planLinePrice->update($attributes);
            Logging::information('Update PlanLinePrice', $attributes, 1, 1);
            return $planLinePrice;
        }

        $planLinePrice = $plan_line->planLinePrices()->create($attributes);
        Logging::information('Create PlanLinePrice', $attributes, 1, 1);

        return $planLinePrice;
    }

    /**
     * Store a newly created plan_line_price
     *
     * @return \Illuminate\Http\Response
     */
    public function create(array $data, PlanLine $plan_line)
    {
        $params = isset($data['plan_line_prices']) ? $data['plan_line_prices'] : $data;
        if (isset($params[0])) {
            $planLinePrices = [];
            foreach ($params as $param) {
                $planLinePrices[] = $this->doSave($param, $plan_line);
            }
            return $planLinePrices;
        } else {
            $planLinePrice = $this->doSave($data, $plan_line);

            return QueryBuilder::for(PlanLinePrice::where('id', $planLinePrice->id))
                ->allowedFields(PlanLinePrice::$fields)
                ->allowedIncludes(PlanLinePrice::$scopes)->first();
        }
    }

    /**
     * Store a newly created plan_line
     *
     * @param \App\Models\Http\Requests\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function update(array $data, PlanLinePrice $planLinePrice)
    {
        $attributes = filterArrayByKeys($data, PlanLinePrice::$fields);
        $log['old_values'] = $planLinePrice->getRawDBData();

        $planLinePrice->update($attributes);
        $log['new_values'] = $planLinePrice->getRawDBData();
        $log['changes'] = $planLinePrice->getChanges();

        Logging::information('Update PlanLinePrice', $log, 1, 1);

        return QueryBuilder::for(PlanLinePrice::where('id', $planLinePrice->id))
            ->allowedFields(PlanLinePrice::$fields)
            ->allowedIncludes(PlanLinePrice::$scopes);
    }

    public function delete(PlanLinePrice $planLinePrice)
    {
        Logging::information('Delete PlanLinePrice', $planLinePrice, 1, 1);
        $planLinePrice->delete();
        return $planLinePrice;
    }
}
