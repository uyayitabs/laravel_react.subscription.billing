<?php

use Illuminate\Database\Seeder;

class PlanLinePricesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('plan_line_prices')->delete();
        
        \DB::table('plan_line_prices')->insert(array (
            0 => 
            array (
                'id' => 1,
                'plan_line_id' => 1,
                'parent_plan_line_id' => NULL,
                'fixed_price' => '77.00',
                'margin' => '0.21',
                'price_valid_from' => '2019-12-31 23:59:59',
                'created_at' => '2019-07-09 09:10:11',
                'updated_at' => '2019-07-09 09:10:11',
            ),
            1 => 
            array (
                'id' => 2,
                'plan_line_id' => 2,
                'parent_plan_line_id' => NULL,
                'fixed_price' => '100.10',
                'margin' => '0.21',
                'price_valid_from' => '2019-12-31 23:59:59',
                'created_at' => '2019-07-09 09:10:11',
                'updated_at' => '2019-07-09 09:10:11',
            ),
            2 => 
            array (
                'id' => 3,
                'plan_line_id' => 3,
                'parent_plan_line_id' => NULL,
                'fixed_price' => '57.50',
                'margin' => '0.21',
                'price_valid_from' => '2019-12-31 23:59:59',
                'created_at' => '2019-07-09 09:10:11',
                'updated_at' => '2019-07-09 09:10:11',
            ),
            3 => 
            array (
                'id' => 4,
                'plan_line_id' => 4,
                'parent_plan_line_id' => NULL,
                'fixed_price' => '58.80',
                'margin' => '0.21',
                'price_valid_from' => '2019-12-31 23:59:59',
                'created_at' => '2019-07-09 09:10:11',
                'updated_at' => '2019-07-09 09:10:11',
            ),
            4 => 
            array (
                'id' => 5,
                'plan_line_id' => 5,
                'parent_plan_line_id' => NULL,
                'fixed_price' => '46.90',
                'margin' => '0.21',
                'price_valid_from' => '2019-12-31 23:59:59',
                'created_at' => '2019-07-09 09:10:11',
                'updated_at' => '2019-07-09 09:10:11',
            ),
            5 => 
            array (
                'id' => 6,
                'plan_line_id' => 6,
                'parent_plan_line_id' => NULL,
                'fixed_price' => '100.30',
                'margin' => '0.21',
                'price_valid_from' => '2019-12-31 23:59:59',
                'created_at' => '2019-07-09 09:10:11',
                'updated_at' => '2019-07-09 09:10:11',
            ),
            6 => 
            array (
                'id' => 7,
                'plan_line_id' => 7,
                'parent_plan_line_id' => NULL,
                'fixed_price' => '99.90',
                'margin' => '0.21',
                'price_valid_from' => '2019-12-31 23:59:59',
                'created_at' => '2019-07-09 09:10:11',
                'updated_at' => '2019-07-09 09:10:11',
            ),
            7 => 
            array (
                'id' => 8,
                'plan_line_id' => 8,
                'parent_plan_line_id' => NULL,
                'fixed_price' => '88.80',
                'margin' => '0.21',
                'price_valid_from' => '2019-12-31 23:59:59',
                'created_at' => '2019-07-09 09:10:11',
                'updated_at' => '2019-07-09 09:10:11',
            ),
        ));
        
        
    }
}