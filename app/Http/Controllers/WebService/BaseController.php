<?php

namespace App\Http\Controllers\WebService;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    /**
     * Send success response
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($serviceResponse)
    {
        $response = [
            'success' => $serviceResponse['success'],
            'message' => $serviceResponse['message']
        ];

        return response()->json($response, $serviceResponse['state']);
    }
}
