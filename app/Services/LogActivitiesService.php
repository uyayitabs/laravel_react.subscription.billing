<?php

namespace App\Services;

use App\Models\LogActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\QueryBuilder;
use Carbon\Carbon;
use App\Http\Resources\LogActivityResource;
use App\Http\Resources\BaseResourceCollection;

class LogActivitiesService
{
    public function list(Request $request)
    {
        $query = \Querying::for(LogActivity::class)
            ->enableFillableSelect()
            ->setFilter(request()->get('filter'))
            ->setSortable(request()->get('sort'))
            ->setSelectables(request()->get('select'))
            ->setSearch(request()->get('search'))
            ->defaultSort('-id')
            ->make()
            ->getQuery()
            ->where('tenant_id', currentTenant('id'));

        return $query;
    }

    public function listBy($subscriptionId = null, $related_entity_type = 'subscription')
    {
        if ($subscriptionId) {
            $query = LogActivity::where([
                ['related_entity_type', $related_entity_type],
                ['related_entity_id', $subscriptionId]
            ]);

            if (request()->has('severity')) {
                $severity = request()->query('severity');
                if ($severity != '') {
                    $query->where('severity', 'like', $severity . '%');
                }
            }

            if (request()->has('details')) {
                $details = request()->query('details');
                if ($details != '') {
                    $query->where('json_data', 'like', '%' . $details . '%');
                }
            }

            if (request()->has('username')) {
                $username = request()->query('username');
                if ($username != '') {
                    $query->where('username', 'like', '%' . $username . '%');
                }
            }

            if (request()->has('message')) {
                $message = request()->query('message');
                if ($message != '') {
                    $query->where('message', 'like', '%' . $message . '%');
                }
            }

            if (request()->has('type')) {
                $type = request()->query('type');
                if ($type  != '') {
                    $query->whereHas('facilityType', function ($query) use ($type) {
                        $query->where('description', $type);
                    });
                }
            }

            if (request()->has('date')) {
                $date = request()->query('date');
                if ($date != '') {
                    $from = Carbon::parse($date . ' 00:00:00', 'UTC')->format('Y-m-d H:i:s');
                    $to = Carbon::parse($date . ' 23:59:59', 'UTC')->format('Y-m-d H:i:s');
                    $query->whereBetween('created_at', [$from, $to]);
                }
            }
        } else {
            $query = LogActivity::whereHas('facilityType', function ($query) {
                $query->where('description', 'M7');
            });
        }

        $query->orderBy('hp_timestamp', 'DESC');

        // pagination (page & limit)
        $limit = request()->query('offset', 10);
        $query = $query->paginate($limit);

        // JsonResource implementation
        $query->transform(function (LogActivity $logActivity) {
            return (new LogActivityResource($logActivity));
        });

        return new BaseResourceCollection($query);
    }

    public function recent()
    {

        $severities = [
            'emerg' => 'Emergency',
            'alert' => 'Alert',
            'crit' => 'Critical',
            'err' => 'Error',
            'warn' => 'Warning',
            'notice' => 'Notice',
            'info' => 'Informational',
            'debug' => 'Debug'
        ];

        foreach ($severities as $s => $n) {
            $result[$s] = [];
            $sev = [$s];
            if ($s == 'warn') {
                $sev = ['warn', 'warning'];
            } elseif ($s == 'err') {
                $sev = ['err', 'error'];
            } elseif ($s == 'emerg') {
                $sev = ['emerg', 'panic', 'emergency'];
            } elseif ($s == 'crit') {
                $sev = ['crit', 'critical'];
            }

            $sql = "
                SELECT
                    CONCAT(
                        REPLACE(
                            FROM_UNIXTIME(
                                FLOOR(
                                    UNIX_TIMESTAMP(created_at)/(15*60))*(15*60)
                                ), ' ', 'T'
                        ), 'Z'
                    ) AS `x`,
                    COUNT(`id`) AS `y`
                FROM log_activities
                WHERE severity IN ('" . implode("','", $sev) . "')
                AND tenant_id = " . currentTenant('id') . "
                AND created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
                GROUP BY `x`
                LIMIT 96;
            ";
            $result[$s] = DB::select($sql);
        }
        $i = 0;
        foreach ($result as $s => $r) {
            $return[$i]['name'] = $severities[$s];
            $return[$i]['data'] = $r;
            $i++;
        }

        return $return;
    }
}
