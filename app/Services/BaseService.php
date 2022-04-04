<?php

namespace App\Services;

class BaseService
{
    public function sentPagination($query, $resource)
    {
        $limit = request()->query('offset', 10);

        $result = $query->paginate($limit);
        $resultCount = $result->total();

        $response = [
            'success' => true,
            'message' => 'Relation persons retrieved successfully',
            'data'    => $resource::collection($result->items()),
            'total'   => $resultCount
        ];

        if (config('app.debug')) {
            $response['query'] = $query->toSql();
        }

        return $response;
    }

    public function handleSearchFilters($modelQuery)
    {
        $searchFilter = request()->query("filter", []);

        if (array_key_exists('keyword', $searchFilter)) {
            $value = $searchFilter['keyword'];
            $modelQuery = $modelQuery->search($value);
        }
        return $modelQuery;
    }
}
