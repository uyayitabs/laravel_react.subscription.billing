<?php

use Illuminate\Database\Seeder;

class AccountingPeriodsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('accounting_periods')->delete();
        
        \DB::table('accounting_periods')->insert(array (
            0 => 
            array (
                'id' => 1,
                'tenant_id' => 7,
                'fiscal_year_id' => 1,
                'description' => '01',
                'date_from' => '2018-01-01',
                'date_to' => '2018-01-31',
                'is_closed' => true,
                'created_at' => '2018-01-01 14:55:32',
                'updated_at' => '2018-01-01 14:55:32',
            ),
            1 => 
            array (
                'id' => 2,
                'tenant_id' => 7,
                'fiscal_year_id' => 1,
                'description' => '02',
                'date_from' => '2018-02-01',
                'date_to' => '2018-02-28',
                'is_closed' => true,
                'created_at' => '2018-01-01 14:55:32',
                'updated_at' => '2018-01-01 14:55:32',
            ),
            2 => 
            array (
                'id' => 3,
                'tenant_id' => 7,
                'fiscal_year_id' => 1,
                'description' => '03',
                'date_from' => '2018-03-01',
                'date_to' => '2018-03-31',
                'is_closed' => true,
                'created_at' => '2018-01-01 14:55:32',
                'updated_at' => '2018-01-01 14:55:32',
            ),
            3 => 
            array (
                'id' => 4,
                'tenant_id' => 7,
                'fiscal_year_id' => 1,
                'description' => '04',
                'date_from' => '2018-04-01',
                'date_to' => '2018-04-30',
                'is_closed' => true,
                'created_at' => '2018-01-01 14:55:32',
                'updated_at' => '2018-01-01 14:55:32',
            ),
            4 => 
            array (
                'id' => 5,
                'tenant_id' => 7,
                'fiscal_year_id' => 1,
                'description' => '05',
                'date_from' => '2018-05-01',
                'date_to' => '2018-05-31',
                'is_closed' => true,
                'created_at' => '2018-01-01 14:55:32',
                'updated_at' => '2018-01-01 14:55:32',
            ),
            5 => 
            array (
                'id' => 6,
                'tenant_id' => 7,
                'fiscal_year_id' => 1,
                'description' => '06',
                'date_from' => '2018-06-01',
                'date_to' => '2018-06-30',
                'is_closed' => true,
                'created_at' => '2018-01-01 14:55:32',
                'updated_at' => '2018-01-01 14:55:32',
            ),
            6 => 
            array (
                'id' => 7,
                'tenant_id' => 7,
                'fiscal_year_id' => 1,
                'description' => '07',
                'date_from' => '2018-07-01',
                'date_to' => '2018-07-31',
                'is_closed' => true,
                'created_at' => '2018-01-01 14:55:32',
                'updated_at' => '2018-01-01 14:55:32',
            ),
            7 => 
            array (
                'id' => 8,
                'tenant_id' => 7,
                'fiscal_year_id' => 1,
                'description' => '08',
                'date_from' => '2018-08-01',
                'date_to' => '2018-08-31',
                'is_closed' => true,
                'created_at' => '2018-01-01 14:55:32',
                'updated_at' => '2018-01-01 14:55:32',
            ),
            8 => 
            array (
                'id' => 9,
                'tenant_id' => 7,
                'fiscal_year_id' => 1,
                'description' => '09',
                'date_from' => '2018-09-01',
                'date_to' => '2018-09-30',
                'is_closed' => true,
                'created_at' => '2018-01-01 14:55:32',
                'updated_at' => '2018-01-01 14:55:32',
            ),
            9 => 
            array (
                'id' => 10,
                'tenant_id' => 7,
                'fiscal_year_id' => 1,
                'description' => '10',
                'date_from' => '2018-10-01',
                'date_to' => '2018-10-31',
                'is_closed' => true,
                'created_at' => '2018-01-01 14:55:32',
                'updated_at' => '2018-01-01 14:55:32',
            ),
            10 => 
            array (
                'id' => 11,
                'tenant_id' => 7,
                'fiscal_year_id' => 1,
                'description' => '11',
                'date_from' => '2018-11-01',
                'date_to' => '2018-11-30',
                'is_closed' => true,
                'created_at' => '2018-01-01 14:55:32',
                'updated_at' => '2018-01-01 14:55:32',
            ),
            11 => 
            array (
                'id' => 12,
                'tenant_id' => 7,
                'fiscal_year_id' => 1,
                'description' => '12',
                'date_from' => '2018-12-01',
                'date_to' => '2018-12-31',
                'is_closed' => true,
                'created_at' => '2018-01-01 14:55:32',
                'updated_at' => '2018-01-01 14:55:32',
            ),
            12 => 
            array (
                'id' => 13,
                'tenant_id' => 7,
                'fiscal_year_id' => 2,
                'description' => '01',
                'date_from' => '2019-01-01',
                'date_to' => '2019-01-31',
                'is_closed' => true,
                'created_at' => '2018-01-01 14:55:32',
                'updated_at' => '2019-01-01 14:55:32',
            ),
            13 => 
            array (
                'id' => 14,
                'tenant_id' => 7,
                'fiscal_year_id' => 2,
                'description' => '02',
                'date_from' => '2019-02-01',
                'date_to' => '2019-02-28',
                'is_closed' => true,
                'created_at' => '2018-01-01 14:55:32',
                'updated_at' => '2019-01-01 14:55:32',
            ),
            14 => 
            array (
                'id' => 15,
                'tenant_id' => 7,
                'fiscal_year_id' => 2,
                'description' => '03',
                'date_from' => '2019-03-01',
                'date_to' => '2019-03-31',
                'is_closed' => true,
                'created_at' => '2018-01-01 14:55:32',
                'updated_at' => '2019-01-01 14:55:32',
            ),
            15 => 
            array (
                'id' => 16,
                'tenant_id' => 7,
                'fiscal_year_id' => 2,
                'description' => '04',
                'date_from' => '2019-04-01',
                'date_to' => '2019-04-30',
                'is_closed' => true,
                'created_at' => '2018-01-01 14:55:32',
                'updated_at' => '2019-01-01 14:55:32',
            ),
            16 => 
            array (
                'id' => 17,
                'tenant_id' => 7,
                'fiscal_year_id' => 2,
                'description' => '05',
                'date_from' => '2019-05-01',
                'date_to' => '2019-05-31',
                'is_closed' => true,
                'created_at' => '2018-01-01 14:55:32',
                'updated_at' => '2019-01-01 14:55:32',
            ),
            17 => 
            array (
                'id' => 18,
                'tenant_id' => 7,
                'fiscal_year_id' => 2,
                'description' => '06',
                'date_from' => '2019-06-01',
                'date_to' => '2019-06-30',
                'is_closed' => true,
                'created_at' => '2018-01-01 14:55:32',
                'updated_at' => '2019-01-01 14:55:32',
            ),
            18 => 
            array (
                'id' => 19,
                'tenant_id' => 7,
                'fiscal_year_id' => 2,
                'description' => '07',
                'date_from' => '2019-07-01',
                'date_to' => '2019-07-31',
                'is_closed' => true,
                'created_at' => '2018-01-01 14:55:32',
                'updated_at' => '2019-01-01 14:55:32',
            ),
            19 => 
            array (
                'id' => 20,
                'tenant_id' => 7,
                'fiscal_year_id' => 2,
                'description' => '08',
                'date_from' => '2019-08-01',
                'date_to' => '2019-08-31',
                'is_closed' => true,
                'created_at' => '2018-01-01 14:55:32',
                'updated_at' => '2019-01-01 14:55:32',
            ),
            20 => 
            array (
                'id' => 21,
                'tenant_id' => 7,
                'fiscal_year_id' => 2,
                'description' => '09',
                'date_from' => '2019-09-01',
                'date_to' => '2019-09-30',
                'is_closed' => true,
                'created_at' => '2018-01-01 14:55:32',
                'updated_at' => '2019-01-01 14:55:32',
            ),
            21 => 
            array (
                'id' => 22,
                'tenant_id' => 7,
                'fiscal_year_id' => 2,
                'description' => '10',
                'date_from' => '2019-10-01',
                'date_to' => '2019-10-31',
                'is_closed' => false,
                'created_at' => '2018-01-01 14:55:32',
                'updated_at' => '2019-01-01 14:55:32',
            ),
            22 => 
            array (
                'id' => 23,
                'tenant_id' => 7,
                'fiscal_year_id' => 2,
                'description' => '11',
                'date_from' => '2019-11-01',
                'date_to' => '2019-11-30',
                'is_closed' => false,
                'created_at' => '2018-01-01 14:55:32',
                'updated_at' => '2019-01-01 14:55:32',
            ),
            23 => 
            array (
                'id' => 24,
                'tenant_id' => 7,
                'fiscal_year_id' => 2,
                'description' => '12',
                'date_from' => '2019-12-01',
                'date_to' => '2019-12-31',
                'is_closed' => false,
                'created_at' => '2018-01-01 14:55:32',
                'updated_at' => '2019-01-01 14:55:32',
            ),
            24 => 
            array (
                'id' => 25,
                'tenant_id' => 7,
                'fiscal_year_id' => 3,
                'description' => '01',
                'date_from' => '2020-01-01',
                'date_to' => '2020-01-31',
                'is_closed' => false,
                'created_at' => '2018-01-01 14:55:32',
                'updated_at' => '2018-01-01 14:55:32',
            ),
            25 => 
            array (
                'id' => 26,
                'tenant_id' => 7,
                'fiscal_year_id' => 3,
                'description' => '02',
                'date_from' => '2020-02-01',
                'date_to' => '2020-02-28',
                'is_closed' => false,
                'created_at' => '2018-01-01 14:55:32',
                'updated_at' => '2018-01-01 14:55:32',
            ),
            26 => 
            array (
                'id' => 27,
                'tenant_id' => 7,
                'fiscal_year_id' => 3,
                'description' => '03',
                'date_from' => '2020-03-01',
                'date_to' => '2020-03-31',
                'is_closed' => false,
                'created_at' => '2018-01-01 14:55:32',
                'updated_at' => '2018-01-01 14:55:32',
            ),
            27 => 
            array (
                'id' => 28,
                'tenant_id' => 7,
                'fiscal_year_id' => 3,
                'description' => '04',
                'date_from' => '2020-04-01',
                'date_to' => '2020-04-30',
                'is_closed' => false,
                'created_at' => '2018-01-01 14:55:32',
                'updated_at' => '2018-01-01 14:55:32',
            ),
            28 => 
            array (
                'id' => 29,
                'tenant_id' => 7,
                'fiscal_year_id' => 3,
                'description' => '05',
                'date_from' => '2020-05-01',
                'date_to' => '2020-05-31',
                'is_closed' => false,
                'created_at' => '2018-01-01 14:55:32',
                'updated_at' => '2018-01-01 14:55:32',
            ),
            29 => 
            array (
                'id' => 30,
                'tenant_id' => 7,
                'fiscal_year_id' => 3,
                'description' => '06',
                'date_from' => '2020-06-01',
                'date_to' => '2020-06-30',
                'is_closed' => false,
                'created_at' => '2018-01-01 14:55:32',
                'updated_at' => '2018-01-01 14:55:32',
            ),
            30 => 
            array (
                'id' => 31,
                'tenant_id' => 7,
                'fiscal_year_id' => 3,
                'description' => '07',
                'date_from' => '2020-07-01',
                'date_to' => '2020-07-31',
                'is_closed' => false,
                'created_at' => '2018-01-01 14:55:32',
                'updated_at' => '2018-01-01 14:55:32',
            ),
            31 => 
            array (
                'id' => 32,
                'tenant_id' => 7,
                'fiscal_year_id' => 3,
                'description' => '08',
                'date_from' => '2020-08-01',
                'date_to' => '2020-08-31',
                'is_closed' => false,
                'created_at' => '2018-01-01 14:55:32',
                'updated_at' => '2018-01-01 14:55:32',
            ),
            32 => 
            array (
                'id' => 33,
                'tenant_id' => 7,
                'fiscal_year_id' => 3,
                'description' => '09',
                'date_from' => '2020-09-01',
                'date_to' => '2020-09-30',
                'is_closed' => false,
                'created_at' => '2018-01-01 14:55:32',
                'updated_at' => '2018-01-01 14:55:32',
            ),
            33 => 
            array (
                'id' => 34,
                'tenant_id' => 7,
                'fiscal_year_id' => 3,
                'description' => '10',
                'date_from' => '2020-10-01',
                'date_to' => '2020-10-31',
                'is_closed' => false,
                'created_at' => '2018-01-01 14:55:32',
                'updated_at' => '2018-01-01 14:55:32',
            ),
            34 => 
            array (
                'id' => 35,
                'tenant_id' => 7,
                'fiscal_year_id' => 3,
                'description' => '11',
                'date_from' => '2020-11-01',
                'date_to' => '2020-11-30',
                'is_closed' => false,
                'created_at' => '2018-01-01 14:55:32',
                'updated_at' => '2018-01-01 14:55:32',
            ),
            35 => 
            array (
                'id' => 36,
                'tenant_id' => 7,
                'fiscal_year_id' => 3,
                'description' => '12',
                'date_from' => '2020-12-01',
                'date_to' => '2020-12-31',
                'is_closed' => false,
                'created_at' => '2018-01-01 14:55:32',
                'updated_at' => '2018-01-01 14:55:32',
            ),
        ));
    }
}