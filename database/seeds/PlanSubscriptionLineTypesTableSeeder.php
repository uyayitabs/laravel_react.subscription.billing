<?php

use Illuminate\Database\Seeder;

class PlanSubscriptionLineTypesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('plan_subscription_line_types')->delete();
        
        \DB::table('plan_subscription_line_types')->insert(array (
            0 => 
            array (
                'id' => 1,
                'line_type' => 'Free',
                'description' => 'Free Subscription line type',
                'created_at' => '2019-07-09 09:10:11',
                'updated_at' => '2019-07-09 09:10:11',
            ),
            1 => 
            array (
                'id' => 2,
                'line_type' => 'NRC',
                'description' => 'Non Recuring Cost',
                'created_at' => '2019-07-09 09:10:11',
                'updated_at' => '2019-07-09 09:10:11',
            ),
            2 => 
            array (
                'id' => 3,
                'line_type' => 'MRC',
                'description' => 'Montly Recuring Cost',
                'created_at' => '2019-07-09 09:10:11',
                'updated_at' => '2019-07-09 09:10:11',
            ),
            3 => 
            array (
                'id' => 4,
                'line_type' => 'QRC',
                'description' => 'Quarterly Recurring Cost',
                'created_at' => '2019-07-18 12:35:00',
                'updated_at' => '2019-07-18 12:35:00',
            ),
            4 => 
            array (
                'id' => 5,
                'line_type' => 'YRC',
                'description' => 'Annual recurring cost',
                'created_at' => '2019-07-18 12:35:00',
                'updated_at' => '2019-07-18 12:35:00',
            ),
            5 => 
            array (
                'id' => 6,
                'line_type' => 'Deposit',
                'description' => 'Deposit',
                'created_at' => '2019-07-18 12:35:00',
                'updated_at' => '2019-07-18 12:35:00',
            ),
            6 => 
            array (
                'id' => 7,
                'line_type' => 'Discount',
                'description' => 'Discount',
                'created_at' => '2019-07-18 12:35:00',
                'updated_at' => '2019-07-18 12:35:00',
            ),
            6 => 
            array (
                'id' => 8,
                'line_type' => 'Voice Usage Cost',
                'description' => 'Voice Usage Cost',
                'created_at' => '2019-07-18 12:35:00',
                'updated_at' => '2019-07-18 12:35:00',
            ),
        ));
        
        
    }
}