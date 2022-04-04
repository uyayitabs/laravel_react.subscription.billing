<?php

namespace App\Services;

use Logging;
use App\Models\NetworkOperator;
use App\Models\Network;
use App\Models\Operator;
use App\Models\SubscriptionLine;
use App\Models\SubscriptionLineMeta;
use Illuminate\Support\Facades\DB;

class NetworkOperatorService
{
    /**
     * Get Network Operator list
     *
     * @return mixed
     */
    public function list()
    {
        return NetworkOperator::join('networks', 'networks.id', '=', 'network_operators.network_id')
            ->join('operators', 'operators.id', '=', 'network_operators.operator_id')
            ->select(
                'network_operators.id',
                'network_operators.network_id',
                'network_operators.operator_id',
                DB::raw("CONCAT(networks.name, CONCAT(' / ', operators.name)) AS label")
            )
            ->orderBy('networks.name', 'ASC')
            ->orderBy('operators.name', 'ASC');
    }

    /**
     * Get Network list (drop-down options)
     *
     * @return mixed
     */
    public function networkListOpts()
    {
        $networkQuery = Network::select(DB::raw('name as `label`'), DB::raw('id as `value`'));
        return $networkQuery->orderBy('label', 'ASC')
            ->get()
            ->toArray();
    }

    /**
     * Get network list (drop-down opts)
     *
     * @return mixed
     */
    public function getNetworks()
    {
        $networkQuery = Network::select(
            DB::raw('name as `label`'),
            DB::raw('id as `value`')
        );
        return $networkQuery->orderBy('label', 'ASC');
    }


    /**
     * Get Operator list (drop-down options)
     * @param mixed|null $networkId
     * @return mixed
     */
    public function getOperators($networkId = null)
    {
        $operatorQuery = Operator::select(DB::raw('name as `label`'), DB::raw('id as `value`'));
        if ($networkId) {
            $operatorIds = NetworkOperator::where('network_id', $networkId)
                ->select('operator_id')
                ->get()
                ->toArray();
            $operatorQuery->whereIn('id', $operatorIds);
        }
        return $operatorQuery->orderBy('label', 'ASC');
    }

    /**
     * Create Network Operator
     *
     * @param mixed $data
     * @return mixed
     */
    public function create($data)
    {
        Logging::information('Create Network Operator', $data, 1, 1);
        $networkOperator = NetworkOperator::create($data);
        return $networkOperator;
    }

    /**
     * Update Network Operator
     *
     * @param mixed $networkOperator
     * @param mixed $data
     */
    public function update($networkOperator, $data)
    {
        $log['old_values'] = $networkOperator->getRawDBData();

        $networkOperator->update($data);
        $log['new_values'] = $networkOperator->getRawDBData();
        $log['changes'] = $networkOperator->getChanges();

        Logging::information('Update Network Operator', $log, 1, 1);
    }

    /**
     * Save SubscriptionLineMeta of selected NetworkOperator
     *
     * @param SubscriptionLine $subscriptionLine
     * @param mixed $data
     * @return mixed
     */
    public function saveSubscriptionLineMeta(SubscriptionLine $subscriptionLine, $data)
    {
        $subscriptionLineMeta = null;
        $netOpfilter = [
            ['network_id', '=', $data['network_id']],
            ['operator_id', '=', $data['operator_id']]
        ];
        $networkOperator = NetworkOperator::where($netOpfilter)->first();
        if ($networkOperator) {
            $subscriptionLineMeta = SubscriptionLineMeta::updateOrInsert(
                [
                    'subscription_line_id' => $subscriptionLine->id,
                    'key' => 'network_operator',
                ],
                [
                    'subscription_line_id' => $subscriptionLine->id,
                    'key' => 'network_operator',
                    'value' => $networkOperator->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
            return $subscriptionLineMeta;
        }
        return;
    }
}
