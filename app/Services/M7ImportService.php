<?php

namespace App\Services;

use App\Models\JsonData;
use App\Models\Relation;
use App\Models\Subscription;
use App\Models\Person;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Str;

class M7ImportService
{
    public function processCSVImport($csvFile)
    {
        $invalidData = (new FastExcel())
            ->configureCsv(',', '"', '\n', 'UTF-8')
            ->import(
                $csvFile,
                function ($line) {
                    $this->processM7Import($line);
                }
            );
    }

    public function processM7Import(array $line)
    {
        $stbs = [];
        foreach (explode(';', $line['FS_SOLOCOO::STBs']) as $stb) {
            $stbs[] = implode('', explode(':', trim(Str::after(Str::before($stb, ']'), '['))));
        }

        $customerNumber = $line['NAW CUST NR'];
        $relation = Relation::where('customer_number', $customerNumber)->first();
        $subscription = $relation->subscriptions()->first();

        if ($subscription->jsonDataM7) {
            return;
        }

        $person = $relation->persons()->where('primary', 1)->first();
        if (!$person) {
            $person = $relation->persons()->first();
        }

        $warehouse = $subscription
            ->relation
            ->tenant
            ->warehouses()
            ->where('warehouse_location', $subscription->address_provisioning->id)
            ->first();

        if (!$warehouse) {
            $warehouse = $subscription
                ->relation
                ->tenant
                ->warehouses()
                ->create([
                    'warehouse_location' => $subscription->address_provisioning->id,
                    'description' => $subscription->address_provisioning->full_address,
                    'active_from' => $subscription->subscription_start,
                    'status' => 'ACTIVE'
                ]);
        }

        $subscriptionLines = $subscription->subscriptionLines()->whereHas('product', function ($query) {
            $query->where('backend_api', 'm7');
            $query->whereHas('jsonData', function ($query1) {
                $query1->where('json_data->m7->type', 'stb');
            });
        })->get();

        foreach ($subscriptionLines as $i => $sl) {
            if (isset($stbs[$i])) {
                $stb = $stbs[$i];
                $exists = $sl->product->serial()->where('warehouse_id', $warehouse->id)->where('serial', $stb)->exists();
                if (!$exists) {
                    $sl->product->serial()->create([
                        'warehouse_id' => $warehouse->id,
                        'serial' => $stb,
                        'json_data' => [
                            'serial' => [
                                'mac' => $stb,
                                'serial' => $stb
                            ]
                        ]
                    ]);
                }
                $sl->serial = $stb;
                $sl->save();
            }
        }

        $data = [
            'tenant_id' => $relation->tenant_id,
            'backend_api' => 'm7',
            'json_data' => [
                'm7' => [
                    'name' => $line['FS_SOLOCOO::PREFIX'] . '-' . $line['FS_SOLOCOO::Name'],
                    'transaction' => 'migration',
                    'solocoo_id' => $line['FS_SOLOCOO::UserId'],
                    'registered' => $line['FS_SOLOCOO::Registered'],
                    'status' => 'New',
                    'stbs' => implode(',', $stbs)
                ]
            ]
        ];

        $subscription->jsonDatas()->create($data);
    }
}
