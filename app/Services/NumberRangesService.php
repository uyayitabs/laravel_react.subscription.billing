<?php

namespace App\Services;

use Logging;
use App\Models\NumberRange;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Resources\BaseResourceCollection;
use App\Http\Resources\NumberRangeResource;

class NumberRangesService
{
    /**
     * Get tenant-specific list number_ranges
     *
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request, $tenantId)
    {
        $query = NumberRange::where('tenant_id', $tenantId);

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
        $query->transform(function (NumberRange $numberRange) {
            return (new NumberRangeResource(
                $numberRange,
                'Number range retrieved successfully.',
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

            // switch ($columnName) {
            //     case 'relation_customer_number':
            //         $columnName = 'relation_id';
            //         break;
            //     case 'relation_company_name':
            //         $columnName = 'tenant_id';
            //         break;
            //     case 'relation_primary_person':
            //         $columnName = 'relation_id';
            //         break;
            // }

            $modelQuery->orderBy($columnName, $sequence);
        }
        return $modelQuery;
    }

    /**
     * Return a list of number ranges for select options list
     *
     * @return \Illuminate\Http\Response
     */
    public function optionList(Request $request, $tenantId)
    {
        return $this->list($request, $tenantId)->select("id", "description");
    }

    /**
     * Store a newly created number range
     *
     * @return \Illuminate\Http\Response
     */
    public function create(array $data)
    {
        $attributes = filterArrayByKeys($data, NumberRange::$fields);
        $numberRange = NumberRange::create($attributes);
        Logging::information('Create NumberRange', $attributes, 1, 1);

        return $this->show($numberRange->id);
    }

    /**
     * Return the specified number range
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return (new NumberRangeResource(
            NumberRange::find($id),
            'Number range retrieved successfully.',
            true
        ));
    }

    /**
     * Update the specified number range
     *
     * @param \App\Models\NumberRange $numberRange
     *
     * @return \Illuminate\Http\Response
     */
    public function update(array $data, NumberRange $numberRange)
    {
        $attributes = filterArrayByKeys($data, NumberRange::$fields);
        $log['old_values'] = $numberRange->getRawDBData();

        $numberRange->update($attributes);
        $log['new_values'] = $numberRange->getRawDBData();
        $log['changes'] = $numberRange->getChanges();

        Logging::information('Update NumberRange', $log, 1, 1);

        return $this->list(request(), currentTenant('id'));
    }
}
