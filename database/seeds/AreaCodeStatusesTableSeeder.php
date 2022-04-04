<?php

use Illuminate\Database\Seeder;

class AreaCodeStatusesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('area_code_statuses')->delete();
        
        \DB::table('area_code_statuses')->insert(array (
            0 => 
            array (
                'id' => 1,
                'status' => 'UNDER_INVESTIGATION',
                'created_at' => '2019-07-09 09:07:31',
                'updated_at' => '2019-07-09 09:07:31',
            ),
            1 => 
            array (
                'id' => 2,
                'status' => 'BUNDELING_DEMAND',
                'created_at' => '2019-07-09 09:07:31',
                'updated_at' => '2019-07-09 09:07:31',
            ),
            2 => 
            array (
                'id' => 3,
                'status' => 'WAIT_FOR_BUILDING',
                'created_at' => '2019-07-09 09:07:31',
                'updated_at' => '2019-07-09 09:07:31',
            ),
            3 => 
            array (
                'id' => 4,
                'status' => 'BUILDING',
                'created_at' => '2019-07-09 09:07:31',
                'updated_at' => '2019-07-09 09:07:31',
            ),
            4 => 
            array (
                'id' => 5,
                'status' => 'AVAILABLE',
                'created_at' => '2019-07-09 09:07:31',
                'updated_at' => '2019-07-09 09:07:31',
            ),
            5 => 
            array (
                'id' => 6,
                'status' => 'CANCELED',
                'created_at' => '2019-07-09 09:07:31',
                'updated_at' => '2019-07-09 09:07:31',
            ),
        ));
        
        
    }
}