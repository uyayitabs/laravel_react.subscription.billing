<?php

use Illuminate\Database\Seeder;

class FiscalYearsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('fiscal_years')->delete();
        
        \DB::table('fiscal_years')->insert(array (
            0 => 
            array (
                'id' => 1,
                'tenant_id' => 7,
                'description' => '2018',
                'date_from' => '2018-01-01',
                'date_to' => '2018-12-31',
                'is_closed' => 1,
                'created_at' => '2018-10-08 14:55:32',
                'updated_at' => '2018-10-08 14:55:32',
            ),
            1 => 
            array (
                'id' => 2,
                'tenant_id' => 7,
                'description' => '2019',
                'date_from' => '2019-01-01',
                'date_to' => '2019-12-31',
                'is_closed' => 0,
                'created_at' => '2019-10-08 14:55:32',
                'updated_at' => '2019-10-08 14:55:32',
            ),
            2 => 
            array (
                'id' => 3,
                'tenant_id' => 7,
                'description' => '2020',
                'date_from' => '2020-01-01',
                'date_to' => '2020-12-31',
                'is_closed' => 0,
                'created_at' => '2019-10-08 14:55:32',
                'updated_at' => '2019-10-08 14:55:32',
            ),
        ));
        
        
    }
}