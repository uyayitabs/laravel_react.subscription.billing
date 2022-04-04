<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Carbon\Carbon;

class ValidateSearchOption
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
        if (request()->has('filter') && isset(request()->filter['keyword']) && Str::contains(request()->filter['keyword'], ':')) {
            $pcs = explode(' ', request()->filter['keyword']);
            $keywords = [];
            foreach ($pcs as $i => $pc) {
                if (Str::contains($pc, ':')) {
                    $keywords[] = $pc;
                } else {
                    $keywords[count($keywords) - 1] = $keywords[count($keywords) - 1] . ' ' . $pc;
                }
            }

            foreach ($keywords as $keyword) {
                $searchCol = trim(Str::before($keyword, ':'));
                $searchKeyword = trim(trim(Str::after($keyword, ':')), '"');

                if (
                    in_array($searchCol, [
                    'date',
                    'due_date',
                    'subscription_start',
                    'subscription_stop',
                    'active_from',
                    'active_to',
                    'date_from',
                    'date_to',
                    'plan_start',
                    'plan_stop',
                    'stop',
                    'start',
                    'invoice_date'
                    ])
                ) {
                    try {
                        Carbon::parse($searchKeyword);
                    } catch (\Exception $e) {
                        Logging(
                            "Search",
                            [
                                'error_message' =>  $e->getMessage(),
                                'error_stacktrace' => $e->getTraceAsString()
                            ],
                            1,
                            'err',
                            0
                        );
                        $response = [
                            'success' => false,
                            'message' => Str::replaceArray('?', [$searchCol, $searchKeyword], 'The ? field, value of ? is not valid search option.'),
                        ];
                        return response()->json($response, 500);
                    }
                }
            }

            $routeArray = app('request')->route()->getAction();
            $controllerAction = class_basename($routeArray['controller']);
            list($controller, $action) = explode('@', $controllerAction);
            $searchableCols = $this->searchableCols($controller, $action);
            if ($searchableCols != false && $searchableCols) {
                $keyword = request()->filter['keyword'];

                $pcs = explode(' ', $keyword);
                $options = [];
                foreach ($pcs as $pc) {
                    if (Str::contains($pc, ':')) {
                        $options[] = Str::before($pc, ':');
                    }
                }

                $valid = true;
                $wrong = [];
                foreach ($options as $option) {
                    if (!in_array($option, $searchableCols)) {
                        $valid = false;
                        $wrong[] = $option;
                    }
                }

                if (!$valid) {
                    $field = 'field';
                    $is = 'is';
                    if (count($wrong) > 1) {
                        $field .= 's';
                        $is = 'are';
                    }
                    $response = [
                        'success' => false,
                        'message' => Str::replaceArray('?', [$field, implode(', ', $wrong), $is], 'The ? ? ? not valid search option.'),
                    ];
                    return response()->json($response, 500);
                }
            }
        }

        return $next($request);
    }

    private function searchableCols($controller, $action)
    {
        $controllers = [
            'TenantController' => [
                'action' => [
                    'my',
                    'vatCodes',
                    'groups'
                ]
            ],
            'NumberRangesController' => [
                'action' => [
                    'my'
                ]
            ],
            'PaymentConditionController' => [
                'action' => [
                    'index'
                ]
            ],
            'AccountsController' => [
                'action' => [
                    'my'
                ]
            ],
            'FiscalYearsController' => [
                'action' => [
                    'my'
                ]
            ],
            'JournalsController' => [
                'action' => [
                    'my'
                ]
            ],
            'RelationController' => [
                'action' => [
                    'index',
                    'addresses',
                    'persons',
                    'bankAccounts',
                    'subscriptions',
                    ''
                ]
            ],
            'SalesInvoiceController' => [
                'action' => [
                    'index'
                ]
            ],
            'PlanController' => [
                'action' => [
                    'index',
                    'planLines'
                ]
            ],
            'SubscriptionController' => [
                'action' => [
                    'index'
                ]
            ],
            'ProductController' => [
                'action' => [
                    'index'
                ]
            ],
            'UserController' => [
                'action' => [
                    'index'
                ]
            ]
        ];
        if (!isset($controllers[$controller]) || !in_array($action, $controllers[$controller]['action'])) {
            return false;
        }
        if (isset($controllers[$controller])) {
            if ($action != 'my' && $action != 'index') {
                $model = Str::ucfirst(Str::singular($action));
            } else {
                $pcs = explode('_', Str::snake($controller));
                Arr::pull($pcs, count($pcs) - 1);
                $model = Str::ucfirst(Str::singular(Str::camel(implode('_', $pcs))));
            }
        }

        return app('App\\' . $model)::$searchableCols;
    }
}
