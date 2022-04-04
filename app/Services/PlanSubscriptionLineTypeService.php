<?php

namespace App\Services;

use App\Models\PlanSubscriptionLineType;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class PlanSubscriptionLineTypeService
{
    /**
     * Return a paginated list of the line types.
     *
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        return QueryBuilder::for(PlanSubscriptionLineType::class, request())
            ->allowedFields(PlanSubscriptionLineType::$fields)
            ->allowedFilters(PlanSubscriptionLineType::$fields)
            ->defaultSort('-id')
            ->allowedSorts(PlanSubscriptionLineType::$fields);
    }

    /**
     * Return the list line types with id and name
     *
     * @return \Illuminate\Http\Response
     */
    public function optionList()
    {
        return PlanSubscriptionLineType::select('id', 'line_type as name');
    }
}
