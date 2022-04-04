<?php

namespace App\Http\Middleware;

use Closure;

class CheckRole
{
    protected $user;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->user = $request->user();

        if ($this->user->is_super_admin) {
            return $next($request);
        }

        if ($request->user() == null) {
            return response("Insufficient permission", 403);
        }

        $actions = $request->route()->getAction();
        $controller = explode("\\", $actions['controller']);
        $method = end($controller);
        $method_arr = explode("@", $method);
        $currentRole = $method_arr[0];

        $request_method = $request->method();

        foreach ($this->user->roles as $i => $roles) {
            $module = $roles->role->module;
            if ($module == 'NumberRangesController') {
                $module = 'TenantController';
            }

            $modules[] = $module;
            $access[$module] = $roles->read . $roles->write;
        }

        if (empty($access[$currentRole])) {
            return $next($request);
        } else {
            if ((($request_method == 'POST' ||
                    $request_method == 'PATCH' ||
                    $request_method == 'DELETE' ||
                    $request_method == 'PUT') &&
                    $access[$currentRole] == 11) ||
                ($request_method == 'GET' && ($access[$currentRole] == 10 ||
                    $access[$currentRole] == 11))
            ) {
                return $next($request);
            } else {
                return response("Insufficient permission", 403);
            }
        }

        return response("Insufficient permission", 403);
    }
}
