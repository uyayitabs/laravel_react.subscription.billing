<?php

use Illuminate\Database\Seeder;
use App\Tenant;
use App\ContractPeriod;

class PopulateContractPeriods extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tenants = Tenant::where('id', '<>', 1)->get();

        $contractPeriods = [
            [
                'period' => '1 year',
                'net_days' => 365
            ],
            [
                'period' => '2 years',
                'net_days' => 730
            ],
            [
                'period' => '3 years',
                'net_days' => 1096
            ],
            [
                'period' => '4 years',
                'net_days' => 1460
            ],
            [
                'period' => '5 years',
                'net_days' => 1825
            ]
        ];

        foreach ($tenants as $tenant) {
            $hasContractPeriod = $tenant->contractPeriods()->count() > 0;
            if (!$hasContractPeriod) {
                foreach ($contractPeriods as $inx => $val) {
                    $contractPeriods[$inx]['tenant_id'] = $tenant->id;
                }
                $tenant->contractPeriods()->insert($contractPeriods);
            }
        }
    }
}
