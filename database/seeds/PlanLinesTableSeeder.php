<?php

use Illuminate\Database\Seeder;

class PlanLinesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('plan_lines')->delete();
        
        \DB::table('plan_lines')->insert(array (
            0 => 
            array (
                'id' => 1,
                'plan_id' => 8,
                'product_id' => 4,
                'plan_line_type' => 3,
                'parent_plan_line_id' => NULL,
                'mandatory_line' => 1,
                'plan_start' => '2019-01-01 07:00:00',
                'plan_stop' => '2019-12-31 07:00:00',
                'created_at' => '2019-07-09 09:10:11',
                'updated_at' => '2019-07-09 09:10:11',
            ),
            1 => 
            array (
                'id' => 2,
                'plan_id' => 11,
                'product_id' => 9,
                'plan_line_type' => 2,
                'parent_plan_line_id' => NULL,
                'mandatory_line' => 1,
                'plan_start' => '2019-01-01 07:00:00',
                'plan_stop' => '2019-12-31 07:00:00',
                'created_at' => '2019-07-09 09:10:11',
                'updated_at' => '2019-07-09 09:10:11',
            ),
            2 => 
            array (
                'id' => 3,
                'plan_id' => 12,
                'product_id' => 3,
                'plan_line_type' => 3,
                'parent_plan_line_id' => NULL,
                'mandatory_line' => 1,
                'plan_start' => '2019-01-01 07:00:00',
                'plan_stop' => '2019-12-31 07:00:00',
                'created_at' => '2019-07-09 09:10:11',
                'updated_at' => '2019-07-09 09:10:11',
            ),
            3 => 
            array (
                'id' => 4,
                'plan_id' => 14,
                'product_id' => 2,
                'plan_line_type' => 3,
                'parent_plan_line_id' => NULL,
                'mandatory_line' => 1,
                'plan_start' => '2019-01-01 07:00:00',
                'plan_stop' => '2019-12-31 07:00:00',
                'created_at' => '2019-07-09 09:10:11',
                'updated_at' => '2019-07-09 09:10:11',
            ),
            4 => 
            array (
                'id' => 5,
                'plan_id' => 15,
                'product_id' => 8,
                'plan_line_type' => 3,
                'parent_plan_line_id' => NULL,
                'mandatory_line' => 1,
                'plan_start' => '2019-01-01 07:00:00',
                'plan_stop' => '2019-12-31 07:00:00',
                'created_at' => '2019-07-09 09:10:11',
                'updated_at' => '2019-07-09 09:10:11',
            ),
            5 => 
            array (
                'id' => 6,
                'plan_id' => 16,
                'product_id' => 5,
                'plan_line_type' => 3,
                'parent_plan_line_id' => NULL,
                'mandatory_line' => 1,
                'plan_start' => '2019-01-01 07:00:00',
                'plan_stop' => '2019-12-31 07:00:00',
                'created_at' => '2019-07-09 09:10:11',
                'updated_at' => '2019-07-09 09:10:11',
            ),
            6 => 
            array (
                'id' => 7,
                'plan_id' => 33,
                'product_id' => 6,
                'plan_line_type' => 3,
                'parent_plan_line_id' => NULL,
                'mandatory_line' => 1,
                'plan_start' => '2019-01-01 07:00:00',
                'plan_stop' => '2019-12-31 07:00:00',
                'created_at' => '2019-07-09 09:10:11',
                'updated_at' => '2019-07-09 09:10:11',
            ),
            7 => 
            array (
                'id' => 8,
                'plan_id' => 50,
                'product_id' => 7,
                'plan_line_type' => 3,
                'parent_plan_line_id' => NULL,
                'mandatory_line' => 1,
                'plan_start' => '2019-01-01 07:00:00',
                'plan_stop' => '2019-12-31 07:00:00',
                'created_at' => '2019-07-09 09:10:11',
                'updated_at' => '2019-07-09 09:10:11',
            ),
        ));
        
        
    }
}