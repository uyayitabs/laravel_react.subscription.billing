<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContractPeriodsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        DB::table('contract_periods')->delete();

        DB::table('contract_periods')->insert(array(
            0 =>
            array(
                'id' => 1,
                'tenant_id' => 7,
                'period' => '1 year',
                'net_days' => 365,
                'created_at' => '2020-06-22 11:48:00',
                'updated_at' => NULL,
            ),
            1 =>
            array(
                'id' => 2,
                'tenant_id' => 7,
                'period' => '2 years',
                'net_days' => 730,
                'created_at' => '2020-06-22 11:48:00',
                'updated_at' => NULL,
            ),
            2 =>
            array(
                'id' => 3,
                'tenant_id' => 7,
                'period' => '3 years',
                'net_days' => 1096,
                'created_at' => '2020-06-22 11:48:00',
                'updated_at' => NULL,
            ),
            3 =>
            array(
                'id' => 4,
                'tenant_id' => 7,
                'period' => '4 years',
                'net_days' => 1460,
                'created_at' => '2020-06-22 11:48:00',
                'updated_at' => NULL,
            ),
            4 =>
            array(
                'id' => 5,
                'tenant_id' => 7,
                'period' => '5 years',
                'net_days' => 1825,
                'created_at' => '2020-06-22 11:48:00',
                'updated_at' => NULL,
            ),
            5 =>
            array(
                'id' => 6,
                'tenant_id' => 8,
                'period' => '1 year',
                'net_days' => 365,
                'created_at' => '2020-06-22 11:48:00',
                'updated_at' => NULL,
            ),
            6 =>
            array(
                'id' => 7,
                'tenant_id' => 8,
                'period' => '2 years',
                'net_days' => 730,
                'created_at' => '2020-06-22 11:48:00',
                'updated_at' => NULL,
            ),
            7 =>
            array(
                'id' => 8,
                'tenant_id' => 8,
                'period' => '3 years',
                'net_days' => 1096,
                'created_at' => '2020-06-22 11:48:00',
                'updated_at' => NULL,
            ),
            8 =>
            array(
                'id' => 9,
                'tenant_id' => 8,
                'period' => '4 years',
                'net_days' => 1460,
                'created_at' => '2020-06-22 11:48:00',
                'updated_at' => NULL,
            ),
            9 =>
            array(
                'id' => 10,
                'tenant_id' => 8,
                'period' => '5 years',
                'net_days' => 1825,
                'created_at' => '2020-06-22 11:48:00',
                'updated_at' => NULL,
            ),
            10 =>
            array(
                'id' => 11,
                'tenant_id' => 9,
                'period' => '1 year',
                'net_days' => 365,
                'created_at' => '2020-06-22 11:48:00',
                'updated_at' => NULL,
            ),
            11 =>
            array(
                'id' => 12,
                'tenant_id' => 9,
                'period' => '2 years',
                'net_days' => 730,
                'created_at' => '2020-06-22 11:48:00',
                'updated_at' => NULL,
            ),
            12 =>
            array(
                'id' => 13,
                'tenant_id' => 9,
                'period' => '3 years',
                'net_days' => 1096,
                'created_at' => '2020-06-22 11:48:00',
                'updated_at' => NULL,
            ),
            13 =>
            array(
                'id' => 14,
                'tenant_id' => 9,
                'period' => '4 years',
                'net_days' => 1460,
                'created_at' => '2020-06-22 11:48:00',
                'updated_at' => NULL,
            ),
            14 =>
            array(
                'id' => 15,
                'tenant_id' => 9,
                'period' => '5 years',
                'net_days' => 1825,
                'created_at' => '2020-06-22 11:48:00',
                'updated_at' => NULL,
            ),
            15 =>
            array(
                'id' => 16,
                'tenant_id' => 10,
                'period' => '1 year',
                'net_days' => 365,
                'created_at' => '2020-06-22 11:48:00',
                'updated_at' => NULL,
            ),
            16 =>
            array(
                'id' => 17,
                'tenant_id' => 10,
                'period' => '2 years',
                'net_days' => 730,
                'created_at' => '2020-06-22 11:48:00',
                'updated_at' => NULL,
            ),
            17 =>
            array(
                'id' => 18,
                'tenant_id' => 10,
                'period' => '3 years',
                'net_days' => 1096,
                'created_at' => '2020-06-22 11:48:00',
                'updated_at' => NULL,
            ),
            18 =>
            array(
                'id' => 19,
                'tenant_id' => 10,
                'period' => '4 years',
                'net_days' => 1460,
                'created_at' => '2020-06-22 11:48:00',
                'updated_at' => NULL,
            ),
            19 =>
            array(
                'id' => 20,
                'tenant_id' => 10,
                'period' => '5 years',
                'net_days' => 1825,
                'created_at' => '2020-06-22 11:48:00',
                'updated_at' => NULL,
            ),
            20 =>
            array(
                'id' => 21,
                'tenant_id' => 13,
                'period' => '1 year',
                'net_days' => 365,
                'created_at' => '2020-06-22 11:48:00',
                'updated_at' => NULL,
            ),
            21 =>
            array(
                'id' => 22,
                'tenant_id' => 13,
                'period' => '2 years',
                'net_days' => 730,
                'created_at' => '2020-06-22 11:48:00',
                'updated_at' => NULL,
            ),
            22 =>
            array(
                'id' => 23,
                'tenant_id' => 13,
                'period' => '3 years',
                'net_days' => 1096,
                'created_at' => '2020-06-22 11:48:00',
                'updated_at' => NULL,
            ),
            23 =>
            array(
                'id' => 24,
                'tenant_id' => 13,
                'period' => '4 years',
                'net_days' => 1460,
                'created_at' => '2020-06-22 11:48:00',
                'updated_at' => NULL,
            ),
            24 =>
            array(
                'id' => 25,
                'tenant_id' => 13,
                'period' => '5 years',
                'net_days' => 1825,
                'created_at' => '2020-06-22 11:48:00',
                'updated_at' => NULL,
            ),
            25 =>
            array(
                'id' => 26,
                'tenant_id' => 14,
                'period' => '1 year',
                'net_days' => 365,
                'created_at' => '2020-06-22 11:48:00',
                'updated_at' => NULL,
            ),
            26 =>
            array(
                'id' => 27,
                'tenant_id' => 14,
                'period' => '2 years',
                'net_days' => 730,
                'created_at' => '2020-06-22 11:48:00',
                'updated_at' => NULL,
            ),
            27 =>
            array(
                'id' => 28,
                'tenant_id' => 14,
                'period' => '3 years',
                'net_days' => 1096,
                'created_at' => '2020-06-22 11:48:00',
                'updated_at' => NULL,
            ),
            28 =>
            array(
                'id' => 29,
                'tenant_id' => 14,
                'period' => '4 years',
                'net_days' => 1460,
                'created_at' => '2020-06-22 11:48:00',
                'updated_at' => NULL,
            ),
            29 =>
            array(
                'id' => 30,
                'tenant_id' => 14,
                'period' => '5 years',
                'net_days' => 1825,
                'created_at' => '2020-06-22 11:48:00',
                'updated_at' => NULL,
            ),
        ));
    }
}
