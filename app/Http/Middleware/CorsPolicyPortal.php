<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Str;

class CorsPolicyPortal
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $origin = $request->headers->get('origin');
        if (
            preg_match('/.fiber.nl|.f2x.nl/', $origin) ||
            (env('APP_ENV') != 'production' && ($origin === null || preg_match('/localhost/', $origin)))
        ) {
            return $next($request);
        } else {
            return response("Insufficient permission", 403);
        }
    }
}
