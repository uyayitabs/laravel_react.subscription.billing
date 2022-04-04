<?php

namespace App\Services;

use App\Filters\PlanLineLineTypeSortFilters;
use App\Filters\PlanLineProductSortFilters;
use Logging;
use App\Models\Plan;
use App\Models\PlanLine;
use Spatie\QueryBuilder\QueryBuilder;
use App\Http\Resources\BaseResourceCollection;
use App\Http\Resources\PlanLineResource;
use Spatie\QueryBuilder\AllowedSort;

class PlanLineService extends BaseService
{
    protected $planLinePriceService;

    public function __construct()
    {
        $this->planLinePriceService = new PlanLinePriceService();
    }

    /**
     * Return a paginated list of plan lines
     *
     * @return \Illuminate\Http\Response
     */
    public function list(Plan $plan)
    {
        $query = PlanLine::where('plan_id', $plan->id);

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
        $query->transform(function (PlanLine $planLine) {
            return (new PlanLineResource(
                $planLine,
                'Plan line retrieved successfully.',
                true,
                true
            ));
        });

        return new BaseResourceCollection($query);
    }

    public function handleSorting($modelQuery, $sortFilter)
    {
        if ($sortFilter) {
            $modelQuery = QueryBuilder::for($modelQuery)->allowedSorts([
                'description',
                'plan_stop',
                'plan_start',
                AllowedSort::custom('line_type.line_type', new PlanLineLineTypeSortFilters(), ''),
                AllowedSort::custom('product.description', new PlanLineProductSortFilters(), '')
            ]);
            ;
        }
        return $modelQuery;
    }

    /**
     * Display the specified plan
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return QueryBuilder::for(PlanLine::where('id', $id))->withAll();
    }

    /**
     * Store a newly created plan_line
     *
     * @param \App\Models\Plan $plan
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Plan $plan, array $data)
    {
        $planLines = $data['plan_lines'];

        foreach ($planLines as $plan_line) {
            $planLine = new PlanLine($plan_line);
            $pl = $plan->planLines()->save($planLine);
            if ($pl) {
                if (!isset($plan_line['plan_line_prices'][0])) {
                    $plan_line_price = $plan_line['plan_line_prices'];
                    $this->planLinePriceService->doSave($plan_line_price, $pl);
                } else {
                    foreach ($plan_line['plan_line_prices'] as $plan_line_price) {
                        $this->planLinePriceService->doSave($plan_line_price, $pl);
                    }
                }
            }
        }

        Logging::information('Create Planlines', $planLines, 1, 1);

        return QueryBuilder::for(PlanLine::where('plan_id', $plan->id))
            ->allowedFields(PlanLine::$fields)
            ->allowedIncludes(PlanLine::$scopes);
    }

    /**
     * Update selected plan_line
     *
     * @return \Illuminate\Http\Response
     */
    public function update(array $data, PlanLine $planLine)
    {
        $attributes = filterArrayByKeys($data, PlanLine::$fields);
        $log['old_values'] = $planLine->getRawDBData();

        $planLine->update($attributes);
        $log['new_values'] = $planLine->getRawDBData();
        $log['changes'] = $planLine->getChanges();

        Logging::information('Update Planline', $log, 1, 1);

        return QueryBuilder::for(PlanLine::where('id', $planLine->id))
            ->allowedFields(PlanLine::$fields)
            ->allowedIncludes(PlanLine::$scopes);
    }
}
