<?php

namespace App\Http\Middleware;

use Closure;
use Jajo\JSONDB;
use Illuminate\Support\Facades\Storage;

class CheckForMaintenanceMode
{
    public function handle($request, Closure $next)
    {
        $exists = \File::exists(storage_path('app/json/config.json'));
        $maintenance = [];
        $response = $next($request);
        if ($exists) {
            $json_db = new JSONDB(storage_path('app/json'));
            $maintenance = $json_db->select('*')->from('config.json')->where(['config' => 'maintenance'])->get();
            if (!empty($maintenance) && $maintenance[0]['value']) {
                $response = response("Under maintenance", 503);
            }
        }

        return $response;
    }
}
