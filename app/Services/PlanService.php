<?php

namespace App\Services;

use Logging;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Resources\BaseResourceCollection;
use App\Http\Resources\PlanResource;

class PlanService extends BaseService
{
    /**
     * Return a paginated list of plans
     *
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        return \Querying::for(Plan::class)
            ->enableFillableSelect()
            ->setFilter(request()->get('filter'))
            ->setSortable(request()->get('sort'))
            ->setSelectables(request()->get('select'))
            ->setSearch(request()->get('search'))
            ->defaultSort('-id')
            ->make()
            ->getQuery()
            ->where('tenant_id', currentTenant('id'));
    }

    public function handleSorting($modelQuery, $sortFilter)
    {
        if ($sortFilter) {
            $sequence = Str::contains($sortFilter, '-') ? 'DESC' : 'ASC';
            $columnName = str_replace('-', '', $sortFilter);

            if ($columnName === 'costs') {
                $selectSql = '(SELECT SUM(fixed_price) FROM plan_lines';
                $selectSql .= ' LEFT JOIN plan_line_prices ON plan_line_prices.plan_line_id = plan_lines.id';
                $selectSql .= ' WHERE plan_lines.plan_id = plans.id) as `costs`';
                $modelQuery->selectRaw('*')
                    ->selectRaw($selectSql);
            }

            $modelQuery->orderBy($columnName, $sequence);
        }
        return $modelQuery;
    }

    /**
     * Store a newly created plan
     *
     * @return \Illuminate\Http\Response
     */
    public function create(array $data)
    {
        $attributes = filterArrayByKeys($data, Plan::$fields);
        $attributes['tenant_id'] = currentTenant('id');
        $plan = Plan::create($attributes);

        Logging::information('Create Plan', $plan, 1, 1);

        return $this->show($plan->id);
    }

    /**
     * Return plan
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return new PlanResource(
            Plan::find($id),
            'Plan retrieved successfully.',
            true
        );
    }

    /**
     * Update plan
     *
     * @param \App\Models\Plan $plan
     *
     * @return \Illuminate\Http\Response
     */
    public function update(array $data, Plan $plan)
    {
        $attributes = filterArrayByKeys($data, Plan::$fields);
        $log['old_values'] = $plan->getRawDBData();

        $plan->update($attributes);
        $log['new_values'] = $plan->getRawDBData();
        $log['changes'] = $plan->getChanges();

        Logging::information('Update Plan', $log, 1, 1);

        $plan->fresh();
        $updateLinePlanStop = array_key_exists('update_line_stop', $attributes) && boolval($attributes['update_line_stop']);

        if ($updateLinePlanStop) {
            $planStop = $plan->plan_stop;
            $planLines = $plan->planLines()
                ->whereNull('plan_stop')
                ->get();

            foreach ($planLines as $planLine) {
                $planLine->update(['plan_stop' => $planStop]);
                Logging::information(
                    'Update Plan Line',
                    [
                        'new_values' => $planLine->getRawDBData(),
                        'changes' => $planLine->getChanges()
                    ],
                    1,
                    1
                );
            }
        }

        return $this->show($plan->id);
    }

    /**
     * Return the list plans with id and name
     *
     * @return \Illuminate\Http\Response
     */
    public function optionList()
    {
        return Plan::active()
            ->complete()
            ->where('tenant_id', currentTenant('id'))
            ->select('id', 'description as name', 'plan_start', 'plan_stop');
    }
}
