<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;

class BaseController extends Controller
{
    public function __construct()
    {
        //TODO
    }
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($result, $message = '', $status = 200)
    {
        $response = [
            'success' => true,
            'message' => $message,
            'data'    => $result,
        ];

        return response()->json($response, $status);
    }

    /**
     * success single response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendSingleResult($query, $message = '')
    {
        $sql = $query->toSql();

        $response = [
            'success' => true,
            'message' => $message,
            'data'    => $query->first()
        ];

        // if (config('app.debug')) {
        //     $response['query'] = $sql;
        // }

        return response()->json($response, 200);
    }

    /**
     * Send error response
     *
     * @return \Illuminate\Http\Response
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
     * @return \Illuminate\Http\Response
     */
    public function sendPaginate($query, $message = '')
    {
        $limit = request()->query('offset', 10);
        $sql = $query->toSql();

        $results = $query->paginate($limit);

        $response = [
            'success' => true,
            'message' => $message,
            'data'    => $results->items(),
            'total'   => $results->total()
        ];

        if (config('app.debug')) {
            $response['query'] = $sql;
        }

        return response()->json($response, 200);
    }

    public function sendNewPaginate($query, $message = '')
    {
        $response = [
            'success' => true,
            'message' => $message,
            'data'    => $query->items(),
            'total'   => $query->total()
        ];

        return response()->json($response, 200);
    }

    /**
     * Send results
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResults($query, $message = '')
    {
        $response = [
            'success' => true,
            'message' => $message,
            'data'    => $query->get()
        ];

        return response()->json($response, 200);
    }

    /**
     * Send single result
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResult($result, $message = '', $status = 200)
    {
        $response = [
            'success' => true,
            'message' => $message,
            'data'    => $result,
        ];

        return response()->json($response, $status);
    }
}
