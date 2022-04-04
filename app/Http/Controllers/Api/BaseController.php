<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PortalSubscriptionResource;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    public function __construct()
    {
        //TODO
    }

    /**
     * success response method.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendResponse($result, $message = '', $status = 200)
    {
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $result,
        ];

        return response()->json($response, $status);
    }

    /**
     * success single response method.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendSingleResult($query, $message = '')
    {
        $sql = $query->toSql();
        $start = Carbon::now();
        $result = $query->first();
        $end = Carbon::now();

        $response = [
            'success' => true,
            'message' => $message,
            'data' => $result
        ];

        if (config('app.debug')) {
            $response['query-duration'] = $start->diff($end)->format('%H:%I:%S.%f');
            $response['query'] = $sql;
        }

        return response()->json($response, 200);
    }

    /**
     * Send error response
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendError($error, $errorMessages = [], $code = 404)
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code);
    }

    /**
     * Send paginated response.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendPaginate($query, $message = '', \Closure $transform = null)
    {
        $limit = request()->query('offset', 10);
        $skip = request()->query('page', 1);
        $start = Carbon::now();
        //$results = $query->paginate($limit);
        if (request()->query('count') !== '0') {
            $count = $query->count();
        }
        $results = $query->skip(($skip - 1) * $limit)->take($limit)->get();
        $end = Carbon::now();
        $sql = $query->toSql();

        // JsonResource implementation
        if ($transform) {
            $results->transform($transform);
        }

        $response = [
            'success' => true,
            'message' => $message,
            //'data' => $results->items(),
            'data' => $results->toArray(),
        ];

        if (isset($count)) {
            $response['total'] = $count;
        }

        if (config('app.debug')) {
            $response['query-duration'] = $start->diff($end)->format('%H:%I:%S.%f');
            $response['query'] = $sql;
        }

        return response()->json($response, 200);
    }

    /**
     * This will check whether the request asks for pagination and if it uses URL Querying
     * If it does NOT use pagination, it should not be used.
     * If it DOES use URL Querying, transforming the response to ApiResources may be needed.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendPaginateOrResult($result, $message = '', \Closure $transform = null)
    {
        $isPaginate = false;
        $isQueryFilter = false;
        if (request()['page'] || request()['offset']) {
            $isPaginate = true;
        }
        if (request()['fields'] || request()['append']) {
            $isQueryFilter = true;
        }

        if ($isPaginate && !$isQueryFilter) {
            return $this->sendPaginate($result, $message, $transform);
        } elseif ($isPaginate && $isQueryFilter) {
            return $this->sendPaginate($result, $message);
        } elseif (!$isPaginate && $isQueryFilter) {
            return $this->sendResult($result, $message);
        } else {
            return $this->sendResults($result, $message, $transform);
        }
    }

    public function sendNewJson($query, $message = '')
    {
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $query->resource->all(),
        ];

        return response()->json($response, 200);
    }

    public function sendNewPaginate($query, $message = '')
    {
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $query->items(),
            'total' => $query->total()
        ];

        return response()->json($response, 200);
    }

    /**
     * Send results
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendResults($query, $message = '', \Closure $transform = null)
    {
        $sql = $query->toSql();
        $start = Carbon::now();
        $data = $query->get();
        $end = Carbon::now();

        if ($transform) {
            $data->transform($transform);
        }

        $response = [
            'success' => true,
            'message' => $message,
            'data' => $data
        ];

        if (config('app.debug')) {
            $response['query-duration'] = $start->diff($end)->format('%H:%I:%S.%f');
            $response['query'] = $sql;
        }

        return response()->json($response, 200);
    }

    /**
     * Send single result
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendResult($result, $message = '', $status = 200)
    {
        $response = [
            'success' => $status == 200 ? true : false,
            'message' => $message,
            'data' => $result,
        ];

        return response()->json($response, $status);
    }

    public function sendServiceResponse($result)
    {
        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
            'data' => $result['data']
        ]);
    }
}
