<?php

use Illuminate\Database\Seeder;
use App\AreaCode;
use App\Network;
use App\Operator;
use App\NetworkOperator;

class NetworkOperatorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $areaCodes = AreaCode::get();

        foreach ($areaCodes as $areaCode) {
            $network = Network::where('name', $areaCode->layer_1)->first();
            $operator = Operator::where('name', $areaCode->layer_2)->first();
            if (!$network) $network = Network::create(['name' => $areaCode->layer_1]);
            if (!$operator) $operator = Operator::create(['name' => $areaCode->layer_2]);

            $exists = NetworkOperator::where([
                [
                    'network_id', '=', $network->id
                ],
                [
                    'operator_id', '=', $operator->id
                ]
            ])->exists();

            if (!$exists) {
                NetworkOperator::create([
                    'network_id' => $network->id,
                    'operator_id' => $operator->id
                ]);
            }
        }
    }
}
