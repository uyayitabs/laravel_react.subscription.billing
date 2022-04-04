<?php

namespace App\Services;

use Logging;
use App\Models\ContractPeriod;
use App\Models\Tenant;

class ContractPeriodService
{
    protected $contractPeriod;

    public function list()
    {
        return Tenant::find(currentTenant('id'))
            ->contractPeriods()
            ->select(['id', 'period', 'net_days']);
    }

    public function create(Tenant $tenant)
    {
        $attributes = request(ContractPeriod::$fields);
        $contractPeriod = $tenant->contractPeriods()->create($attributes);
        return $contractPeriod;
    }

    public function update(ContractPeriod $contractPeriod)
    {
        $attributes = request(ContractPeriod::$fields);

        $log['old_values'] = $contractPeriod->getRawDBData();
        $contractPeriod->update($attributes);
        $log['new_values'] = $contractPeriod->getRawDBData();
        $log['changes'] = $contractPeriod->getChanges();

        Logging::information('Update Contract Period', $log, 1, 1);
        return $contractPeriod;
    }
}
